<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LeaveRequest;
use App\User;
use App\LeaveSetting;
use App\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeaveStatusNotification;
use Carbon\Carbon;

class LeaveController extends Controller
{
    /**
     * Menampilkan daftar pengajuan cuti.
     * Jika role adalah HRD atau Superadmin, tampilkan semua.
     * Jika role adalah Karyawan, tampilkan milik sendiri.
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role ? strtolower($user->role->name) : '';

        if ($role === 'hrd' || $role === 'superadmin') {
            $leaves = LeaveRequest::with('user')->orderBy('created_at', 'desc')->get();
        } else {
            $leaves = LeaveRequest::where('nip', $user->nip)->orderBy('created_at', 'desc')->get();
        }

        return view('leave.index', compact('leaves', 'role'));
    }

    /**
     * Menampilkan form pengajuan cuti (Hanya Karyawan).
     */
    public function create()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        
        $activeLeave = LeaveRequest::where('nip', $user->nip)
            ->whereIn('status', [LeaveRequest::STATUS_PENDING, LeaveRequest::STATUS_APPROVED])
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();

        if ($activeLeave) {
            $formattedDate = Carbon::parse($activeLeave->end_date)->translatedFormat('d F Y');
            return redirect()->route('dashboard')->with('error', "Anda sedang cuti sampai tanggal $formattedDate. Tidak dapat mengajukan cuti baru saat ini.");
        }

        return view('leave.create');
    }

    /**
     * Menyimpan pengajuan cuti baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:255',
        ], [
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'reason.required' => 'Alasan cuti wajib diisi.',
        ]);

        $user = Auth::user();

        // Cek apakah ada catatan absensi (masuk/izin) pada rentang tanggal cuti yang diajukan
        $attendanceInRange = Attendance::where('employee_nip', $user->nip)
            ->whereBetween('date', [$request->start_date, $request->end_date])
            ->first();

        if ($attendanceInRange) {
            $formattedDate = Carbon::parse($attendanceInRange->date)->format('d/m/Y');
            return redirect()->back()->withInput()->with('error', "Anda tidak dapat mengajukan cuti pada rentang tersebut karena Anda sudah memiliki catatan absensi/izin pada tanggal $formattedDate.");
        }

        // Cek apakah ada pengajuan cuti lain (Pending / Approved) yang bertabrakan dengan rentang tanggal ini
        $overlappingLeave = LeaveRequest::where('nip', $user->nip)
            ->whereIn('status', [LeaveRequest::STATUS_PENDING, LeaveRequest::STATUS_APPROVED])
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                      });
            })
            ->first();

        if ($overlappingLeave) {
            $overlapStart = \Carbon\Carbon::parse($overlappingLeave->start_date)->format('d/m/Y');
            $overlapEnd = \Carbon\Carbon::parse($overlappingLeave->end_date)->format('d/m/Y');
            return redirect()->back()->withInput()->with('error', "Anda sudah memiliki pengajuan cuti yang bertabrakan pada rentang $overlapStart hingga $overlapEnd.");
        }

        $setting = LeaveSetting::first();
        
        // Hitung jumlah hari cuti
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $days = $start->diffInDays($end) + 1; // Termasuk hari terakhir

        // Cek maksimal hari per pengajuan
        if ($setting && $days > $setting->max_days_per_request) {
            return redirect()->back()->withInput()->with('error', 'Maksimal cuti per pengajuan adalah ' . $setting->max_days_per_request . ' hari.');
        }

        // Cek sisa cuti
        if ($user->sisa_cuti < $days) {
            return redirect()->back()->withInput()->with('error', 'Sisa cuti tidak mencukupi. Sisa cuti Anda: ' . $user->sisa_cuti . ' hari.');
        }

        // Simpan pengajuan
        LeaveRequest::create([
            'nip' => $user->nip,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days' => $days,
            'reason' => $request->reason,
            'status' => LeaveRequest::STATUS_PENDING,
        ]);

        return redirect()->route('leave.index')->with('success', 'Pengajuan cuti berhasil dikirim dan menunggu persetujuan HRD.');
    }

    /**
     * Menyetujui pengajuan cuti (Hanya HRD/Superadmin).
     */
    public function approve(Request $request, $id)
    {
        $leave = LeaveRequest::findOrFail($id);
        
        if ($leave->status !== LeaveRequest::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Hanya pengajuan PENDING yang bisa disetujui.');
        }

        $employee = $leave->user;

        // Validasi ulang sisa cuti saat approval
        if ($employee->sisa_cuti < $leave->days) {
            $leave->update([
                'status' => LeaveRequest::STATUS_REJECTED,
                'hrd_note' => 'Otomatis ditolak sistem: Sisa cuti tidak mencukupi saat proses approval.'
            ]);
            return redirect()->back()->with('error', 'Sisa cuti karyawan tidak mencukupi. Pengajuan otomatis ditolak.');
        }

        // Update status pengajuan
        $leave->update([
            'status' => LeaveRequest::STATUS_APPROVED,
            'hrd_note' => $request->hrd_note ?? null
        ]);

        // Potong jatah cuti karyawan
        $employee->increment('cuti_terpakai', $leave->days);
        $employee->decrement('sisa_cuti', $leave->days);

        // Kirim email notifikasi
        try {
            if ($employee->email) {
                Mail::to($employee->email)->send(new LeaveStatusNotification($leave));
            }
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim email notifikasi cuti (Approval): ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Pengajuan cuti berhasil disetujui.');
    }

    /**
     * Menolak pengajuan cuti (Hanya HRD/Superadmin).
     */
    public function reject(Request $request, $id)
    {
        $leave = LeaveRequest::findOrFail($id);
        
        if ($leave->status !== LeaveRequest::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Hanya pengajuan PENDING yang bisa ditolak.');
        }

        $leave->update([
            'status' => LeaveRequest::STATUS_REJECTED,
            'hrd_note' => $request->hrd_note ?? null
        ]);

        // Kirim email notifikasi
        try {
            $employee = $leave->user;
            if ($employee && $employee->email) {
                Mail::to($employee->email)->send(new LeaveStatusNotification($leave));
            }
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim email notifikasi cuti (Rejection): ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Pengajuan cuti telah ditolak.');
    }
}
