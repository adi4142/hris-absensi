<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Attendance;
use App\Payroll;
use App\PayrollDetail;
use App\PayrollComponent;
use App\Position;
use App\Departement;
use App\Division;
use App\User;
use App\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $roleName = $user->role ? strtolower($user->role->name) : '';
        
        // Check if user is Karyawan
        if ($roleName === 'karyawan') {
            // Stats for Dashboard - Counts for current month
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            $totalAttendance = Attendance::where('employee_nip', $user->nip)
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->count();
            
            $totalLate = Attendance::where('employee_nip', $user->nip)
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->where('status', 'Late')
                ->count();

            $totalPermission = Attendance::where('employee_nip', $user->nip)
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->where('status', 'Permission')
                ->count();

            $totalAlpha = Attendance::where('employee_nip', $user->nip)
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->where('status', 'Alpha')
                ->count();

            // Attendance History (Last 5 records)
            $attendanceHistory = Attendance::where('employee_nip', $user->nip)
                ->orderBy('date', 'desc')
                ->limit(5)
                ->get();
            
            $today = Carbon::today()->toDateString();
            $todayAttendance = Attendance::where('employee_nip', $user->nip)
                ->where('date', $today)
                ->first();

            $activeLeave = LeaveRequest::where('nip', $user->nip)
                ->whereIn('status', [LeaveRequest::STATUS_PENDING, LeaveRequest::STATUS_APPROVED])
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->first();

            return view('dashboard.employee_dashboard', [
                'employee' => $user, 
                'totalAttendance' => $totalAttendance, 
                'totalLate' => $totalLate, 
                'totalPermission' => $totalPermission, 
                'totalAlpha' => $totalAlpha, 
                'attendanceHistory' => $attendanceHistory, 
                'todayAttendance' => $todayAttendance,
                'activeLeave' => $activeLeave
            ]);
        } elseif ($roleName === 'hrd' || $roleName === 'admin' || $roleName === 'superadmin') {
            // Admin/HRD/Superadmin Dashboard Logic
            $today = Carbon::now()->format('Y-m-d');
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            
            // 0. General Stats
            $totalEmployees = User::whereNotNull('nip')->count();
            $totalDepartments = Departement::count();
            $totalPositions = Position::count();
            $totalDivisions = Division::count();

            // 1. Attendance Stats
            $totalPresent = Attendance::whereDate('date', $today)->where('status', 'Present')->count();
            $totalPermission = Attendance::whereDate('date', $today)->where('status', 'Permission')->count();
            $totalAlpha = Attendance::whereDate('date', $today)->where('status', 'Alpha')->count();
            $totalLate = Attendance::whereDate('date', $today)->where('status', 'Late')->count();

            // 2. Today's Attendance Table
            $todayAttendances = Attendance::with(['employee.position', 'employee.division'])
                ->whereDate('date', $today)
                ->orderBy('time_in', 'desc')
                ->get();

            // 3. Payroll Stats
            $currentPayroll = Payroll::where('period_month', $currentMonth)
                ->where('period_year', $currentYear)
                ->first();

            if (!$currentPayroll || $currentPayroll->details()->count() == 0) {
                $fallbackPayroll = Payroll::has('details')
                    ->orderBy('period_year', 'desc')
                    ->orderBy('period_month', 'desc')
                    ->first();
                
                if ($fallbackPayroll) {
                    $currentPayroll = $fallbackPayroll;
                }
            }

            $totalSalaryPaid = 0;
            $totalDeductions = 0;
            $totalAllowances = 0;
            $payrollDetails = collect();

            if ($currentPayroll) {
                $totalSalaryPaid = $currentPayroll->details()->sum('total_salary');
                $totalDeductions = $currentPayroll->details()->sum('total_deduction');
                $totalAllowances = $currentPayroll->details()->sum('total_allowance');
                $payrollDetails = $currentPayroll->details()->with('employee')->limit(5)->get();
            }

            $pendingLeaves = LeaveRequest::with('user')->where('status', LeaveRequest::STATUS_PENDING)->orderBy('created_at', 'desc')->limit(5)->get();

            // HRD and Superadmin show the detailed dashboard/index
            if ($roleName === 'hrd' || $roleName === 'admin') {
                return view('dashboard.hrd', compact(
                    'totalEmployees', 'totalDepartments', 'totalPositions', 'totalDivisions',
                    'totalPresent', 'totalPermission', 'totalAlpha', 'totalLate',
                    'todayAttendances', 'totalSalaryPaid', 'totalDeductions', 'totalAllowances',
                    'payrollDetails', 'currentPayroll', 'pendingLeaves'
                ));
            }

            // For Superadmin, show a master dashboard
            $recentUsers = User::with(['position', 'division'])->orderBy('created_at', 'desc')->limit(5)->get();
            $pendingLeaves = LeaveRequest::with('user')->where('status', LeaveRequest::STATUS_PENDING)->orderBy('created_at', 'desc')->limit(5)->get();
            
            return view('dashboard.superadmin', compact(
                'totalEmployees', 'totalDepartments', 'totalPositions', 'totalDivisions',
                'totalPresent', 'totalPermission', 'totalAlpha', 'totalLate',
                'recentUsers', 'pendingLeaves'
            ));
        }

        return abort(403, 'Role: ' . ($roleName ?: 'EMPTY'));
    }

    public function monitoring(Request $request)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        
        // Eager load attendance for the selected date
        $employees = User::whereNotNull('nip')->with(['position', 'division', 'attendances' => function($query) use ($date) {
            $query->whereDate('date', $date);
        }])->get();

        return view('attendance.monitoring', compact('employees', 'date'));
    }

    public function employeeHistory(Request $request, $nip)
    {
        $employee = User::findOrFail($nip);
        
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $attendances = Attendance::where('employee_nip', $employee->nip)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get();

        return view('attendance.employee_history', compact('employee', 'attendances', 'month', 'year'));
    }

    public function index()
    {
        $user = Auth::user();

        if ($user && $user->role && strtolower($user->role->name) === 'karyawan') {
             // For Karyawan, show their OWN attendance history in the index view
             $attendances = Attendance::with('employee')
                ->where('employee_nip', $user->nip)
                ->orderBy('date', 'desc')
                ->orderBy('time_in', 'desc')
                ->get();
        } else {
             // Default Admin/HRD View (Show All)
             $attendances = Attendance::with('employee')->orderBy('date', 'desc')->orderBy('time_in', 'desc')->get();
        }

        return view('attendance.index', compact('attendances'));
    }

    public function absensi()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $now = Carbon::now()->toTimeString();

        if ($user && $user->role && strtolower($user->role->name) === 'karyawan') {
             // For Karyawan, show their OWN attendance history
             $attendances = Attendance::with('employee')
                ->where('employee_nip', $user->nip)
                ->orderBy('date', 'desc')
                ->orderBy('time_in', 'desc')
                ->get();
        } else {
             // Default Admin/HRD View (Show All)
             $attendances = Attendance::with('employee')->orderBy('date', 'desc')->orderBy('time_in', 'desc')->get();
        }

        $todayAttendance = Attendance::where('employee_nip', $user->nip)
            ->where('date', $today)
            ->first();
        
        $activeLeave = LeaveRequest::where('nip', $user->nip)
            ->whereIn('status', [LeaveRequest::STATUS_PENDING, LeaveRequest::STATUS_APPROVED])
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();

        return view('attendance.employee_absensi', compact('attendances', 'todayAttendance', 'today', 'activeLeave'));
    }

    public function scan()
    {
        $user = Auth::user();
        $attendance = null;
        $position = null;

        if ($user) {
            $position = $user->position;
            $today = Carbon::today()->toDateString();
            $attendance = Attendance::where('employee_nip', $user->nip)
                ->where('date', $today)
                ->first();

            $activeLeave = LeaveRequest::where('nip', $user->nip)
                ->whereIn('status', [LeaveRequest::STATUS_PENDING, LeaveRequest::STATUS_APPROVED])
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->first();

            if ($activeLeave) {
                $formattedDate = Carbon::parse($activeLeave->end_date)->translatedFormat('d F Y');
                return redirect()->route('dashboard')->with('error', "Anda sedang cuti sampai tanggal $formattedDate. Anda tidak dapat melakukan scan absensi saat ini.");
            }
        }

        return view('attendance.scan', compact('user', 'attendance', 'position', 'activeLeave'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required',
            'attendance_code' => 'required|exists:users,attendance_code',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        // Find Employee by Attendance Code
        $employee = User::where('attendance_code', $request->attendance_code)->first();
        if (!$employee) {
             return response()->json([
                'success' => false,
                'message' => 'Kode Absensi tidak valid.'
            ]);
        }
        
        $currentNip = $employee->nip;
        $today = Carbon::today()->toDateString();

        // Cek apakah karyawan memiliki cuti (PENDING atau APPROVED) pada hari ini
        $activeLeave = LeaveRequest::where('nip', $currentNip)
            ->whereIn('status', [LeaveRequest::STATUS_PENDING, LeaveRequest::STATUS_APPROVED])
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();

        if ($activeLeave) {
            $statusLabel = ($activeLeave->status === LeaveRequest::STATUS_APPROVED) ? 'disetujui' : 'diajukan (menunggu persetujuan)';
            return response()->json([
                'success' => false,
                'message' => "Anda tidak bisa melakukan absensi karena Anda memiliki cuti yang sedang $statusLabel untuk hari ini."
            ]);
        }

        // Ambil pengaturan lokasi kantor dari system settings
        $officeLat = \App\SystemSetting::get('office_latitude', -6.235306374734767); 
        $officeLng = \App\SystemSetting::get('office_longitude', 106.78080237228927); 
        
        // Jarak Maksimal (dalam meter)
        $maxDistance = \App\SystemSetting::get('attendance_radius', 100);

        $distance = $this->calculateDistance($request->latitude, $request->longitude, $officeLat, $officeLng);

        if ($distance > $maxDistance) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada diluar jangkauan kantor. Jarak anda: ' . round($distance) . ' meter.'
            ]);
        }

        $img = $request->image;
        $folderPath = "public/attendance/";
        
        $image_parts = explode(";base64,", $img);
        
        // Handle potential error if image format is invalid
        if (count($image_parts) < 2) {
             return response()->json(['success' => false, 'message' => 'Format gambar tidak valid.']);
        }

        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $currentNip . '_' . time() . '.png';
        
        $file = $folderPath . $fileName;
        Storage::put($file, $image_base64);

        $today = Carbon::today()->toDateString();
        $now = Carbon::now()->toTimeString();

        // Ambil jam masuk kerja dari pengaturan
        $lateThreshold = \App\SystemSetting::get('work_start_time', '08:00:00');
        // Tambahkan detik jika formatnya hanya HH:mm
        if (strlen($lateThreshold) == 5) $lateThreshold .= ':00';
        
        $status = ($now > $lateThreshold) ? 'Late' : 'Present';

        $attendance = Attendance::where('employee_nip', $currentNip)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            // Check-in
            Attendance::create([
                'employee_nip' => $currentNip,
                'date' => $today,
                'time_in' => $now,
                'photo_in' => $fileName,
                'status' => $status
            ]);

            // Apply Deduction if Late
            if ($status === 'Late') {
                // $this->applyDeduction($request->nip, 'Potongan Keterlambatan', $latePenalty); // Dihitung otomatis saat Generate Payroll
                return response()->json([
                    'success' => true, 
                    'message' => 'Berhasil Absen Masuk! (Anda Terlambat)'
                ]);
            }

            return response()->json([
                'success' => true, 
                'message' => 'Berhasil Absen Masuk! Selamat bekerja.'
            ]);
        } else {
            // Check if existing record is Permission/Alpha
            if ($attendance->status == 'Permission' || $attendance->status == 'Alpha') {
                 return response()->json([
                    'success' => false, 
                    'message' => 'Anda sudah mengajukan izin/sakit atau alpha hari ini. Tidak bisa absen.'
                ]);
            }

            // Check-out logic
            if ($attendance->time_out) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Anda sudah melakukan absen keluar hari ini.'
                ]);
            }

            // Ambil jam pulang kerja dari pengaturan
            $workEndTime = \App\SystemSetting::get('work_end_time', '17:00');
            // Normalisasi format: ambil hanya HH:mm agar perbandingan konsisten
            $workEndTimeFormatted = substr($workEndTime, 0, 5); // ambil 'HH:mm'
            $currentTime = Carbon::now()->format('H:i');
            
            if ($currentTime < $workEndTimeFormatted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Absen keluar hanya bisa dilakukan setelah jam ' . $workEndTimeFormatted . '.'
                ]);
            }

            $attendance->update([
                'time_out' => $now,
                'photo_out' => $fileName
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Berhasil Absen Keluar! Hati-hati di jalan.'
            ]);
        }
    }

    /**
     * Hitung Jarak antara dua titik koordinat (Haversine Formula)
     * Return dalam satuan Meter
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos($lat1) * cos($lat2) *
             sin($dLon / 2) * sin($dLon / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    private function applyDeduction($nip, $name, $amount)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Find Payroll for current period, or Create it
        $payroll = Payroll::firstOrCreate(
            ['period_month' => $currentMonth, 'period_year' => $currentYear],
            ['status' => 'calculated']
        );
        
        // Find Payroll Detail for this employee, or Create it
        $detail = PayrollDetail::firstOrCreate(
            ['payroll_id' => $payroll->payroll_id, 'nip' => $nip],
            [
                'basic_salary' => 0,
                'total_allowance' => 0,
                'total_deduction' => 0,
                'total_salary' => 0
            ]
        );
        
        // Create Component
        PayrollComponent::create([
            'payroll_detail_id' => $detail->payroll_detail_id,
            'name' => $name,
            'type' => 'deduction',
            'amount' => $amount
        ]);

        // Update total_deduction and total_salary
        $detail->total_deduction += $amount;
        $detail->total_salary = $detail->basic_salary + $detail->total_allowance - $detail->total_deduction;
        $detail->save();
    }

    public function applyAlphaDeduction($nip)
    {
        // Example penalty for Alpha (Absent without notice)
        $alphaPenalty = 100000; 
        $this->applyDeduction($nip, 'Potongan Alpha', $alphaPenalty);
    }

    public function history(Request $request)
    {
        $user = Auth::user();

        $query = Attendance::where('employee_nip', $user->nip)
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc');

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $attendances = $query->get();

        return view('attendance.history', compact('attendances'));
    }

    public function createPermission()
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
            return redirect()->route('dashboard')->with('error', "Anda sedang cuti sampai tanggal $formattedDate. Tidak dapat mengajukan izin baru saat ini.");
        }

        return view('attendance.permission_create');
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'status' => 'required|in:Permission,Alpha', // Alpha only accessible by admin ideally, but user asked for "Form Izin", so Permission mostly.
            'description' => 'required|string',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = Auth::user();
        
        // Cek apakah ada cuti pada tanggal tersebut
        $activeLeave = LeaveRequest::where('nip', $user->nip)
            ->whereIn('status', [LeaveRequest::STATUS_PENDING, LeaveRequest::STATUS_APPROVED])
            ->where('start_date', '<=', $request->date)
            ->where('end_date', '>=', $request->date)
            ->exists();

        if ($activeLeave) {
            return back()->with('error', 'Anda tidak bisa mengajukan izin pada tanggal tersebut karena sudah memiliki pengajuan cuti.');
        }

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')->store('public/permission_proofs');
        }

        // Check if attendance already exists for date
        $exists = Attendance::where('employee_nip', $user->nip)
            ->where('date', $request->date)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Anda sudah melakukan absensi atau mengajukan izin pada tanggal tersebut.');
        }

        Attendance::create([
            'employee_nip' => $user->nip,
            'date' => $request->date,
            'status' => $request->status, // Permission
            'description' => $request->description,
            'proof_file' => $proofPath,
            // Time in/out null for Permission
        ]);

        return redirect()->route('attendance.history')->with('success', 'Pengajuan izin berhasil disimpan.');
    }

    public function shortcutPermission()
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
            return redirect()->route('dashboard')->with('error', "Anda sedang cuti sampai tanggal $formattedDate. Tidak dapat mengajukan izin baru saat ini.");
        }

        return view('attendance.shortcut_permission');
    }
}
