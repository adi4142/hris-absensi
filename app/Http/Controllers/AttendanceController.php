<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Attendance;
use App\Employee;
use App\Payroll;
use App\PayrollDetail;
use App\PayrollComponent;
use App\Position;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Check if user is Karyawan
        if ($user && $user->role && strtolower($user->role->name) === 'karyawan') {
            $employee = Employee::where('user_id', $user->user_id)->first();

            if (!$employee) {
                return abort(403, 'Data karyawan tidak ditemukan untuk akun ini.');
            }

            // Stats for Dashboard - Counts for current month
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            $totalAttendance = Attendance::where('employee_nip', $employee->nip)
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->count();
            
            $totalLate = Attendance::where('employee_nip', $employee->nip)
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->where('status', 'Late')
                ->count();

            // Note: Permission and Alpha might need a separate table or status enum, assuming 'Permission' and 'Alpha' status exists or similar logic. 
            // For now, I'll assume status field handles this or it's 0 if not implemented yet.
            $totalPermission = Attendance::where('employee_nip', $employee->nip)
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->where('status', 'Permission')
                ->count();

            $totalAlpha = Attendance::where('employee_nip', $employee->nip)
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->where('status', 'Alpha')
                ->count();

            // Attendance History (Last 5 records)
            $attendanceHistory = Attendance::where('employee_nip', $employee->nip)
                ->orderBy('date', 'desc')
                ->limit(5)
                ->get();
            
            $today = Carbon::today()->toDateString();
            $todayAttendance = Attendance::where('employee_nip', $employee->nip)
                ->where('date', $today)
                ->first();

            return view('attendance.employee_dashboard', compact(
                'employee', 
                'totalAttendance', 
                'totalLate', 
                'totalPermission', 
                'totalAlpha', 
                'attendanceHistory', 
                'todayAttendance'
            ));
        } elseif ($user && $user->role && strtolower($user->role->name) === 'hrd') {
            // HRD Dashboard Logic
            $today = Carbon::now()->format('Y-m-d');
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

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

            // If no payroll for current month or it has no details, get the latest one that has details
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
                $payrollDetails = $currentPayroll->details()->with('employee')->limit(5)->get(); // Limit optimized
            }

            return view('dashboard.hrd', compact(
                'totalPresent',
                'totalPermission',
                'totalAlpha',
                'totalLate',
                'todayAttendances',
                'totalSalaryPaid',
                'totalDeductions',
                'totalAllowances',
                'payrollDetails',
                'currentPayroll'
            ));
        }

        // Default Dashboard for Admin/Other
        return view('welcome');
    }

    public function monitoring(Request $request)
    {
        $today = Carbon::now()->format('Y-m-d');
        // Eager load attendance for today
        $employees = Employee::with(['position', 'division', 'attendance' => function($query) use ($today) {
            $query->whereDate('date', $today);
        }])->get();

        return view('attendance.monitoring', compact('employees', 'today'));
    }

    public function employeeHistory(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        
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
             $employee = Employee::where('user_id', $user->user_id)->first();
             if ($employee) {
                 $attendances = Attendance::with('employee')
                    ->where('employee_nip', $employee->nip)
                    ->orderBy('date', 'desc')
                    ->orderBy('time_in', 'desc')
                    ->get();
             } else {
                 $attendances = collect(); // Empty collection if no employee data
             }
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
             // For Karyawan, show their OWN attendance history in the index view
             $employee = Employee::where('user_id', $user->user_id)->first();
             if ($employee) {
                 $attendances = Attendance::with('employee')
                    ->where('employee_nip', $employee->nip)
                    ->orderBy('date', 'desc')
                    ->orderBy('time_in', 'desc')
                    ->get();
             } else {
                 $attendances = collect(); // Empty collection if no employee data
             }
        } else {
             // Default Admin/HRD View (Show All)
             $attendances = Attendance::with('employee')->orderBy('date', 'desc')->orderBy('time_in', 'desc')->get();
        }

        $today = Carbon::today()->toDateString();
        $todayAttendance = Attendance::where('employee_nip', $employee->nip)
            ->where('date', $today)
            ->first();
        $attendance = Attendance::where('employee_nip', $employee->nip)
            ->where('date', $today)
            ->first();
        return view('attendance.employee_absensi', compact('attendances', 'todayAttendance', 'today', 'attendance'));
    }

    public function scan()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $employee = Employee::where('user_id', $user->user_id)->first();
        $position = Position::where('position_id', $employee->position_id)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Akun anda tidak terhubung dengan data karyawan.');
        }

        $today = Carbon::today()->toDateString();
        $attendance = Attendance::where('employee_nip', $employee->nip)
            ->where('date', $today)
            ->first();

        return view('attendance.scan', compact('employee', 'attendance', 'position'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required',
            'nip' => 'required|exists:employees,nip',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        // Lokasi Kantor (Ganti dengan koordinat kantor Anda)
        // Contoh: Monas, Jakarta Pusat
        $officeLat = -6.175110; 
        $officeLng = 106.827153; 
        
        // Jarak Maksimal (dalam meter)
        $maxDistance = 100;

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
        $fileName = $request->nip . '_' . time() . '.png';
        
        $file = $folderPath . $fileName;
        Storage::put($file, $image_base64);

        $today = Carbon::today()->toDateString();
        $now = Carbon::now()->toTimeString();

        // Check if Late (Example: Late after 08:00 AM)
        $lateThreshold = '07:00:00';
        $status = ($now > $lateThreshold) ? 'Late' : 'Present';
        $latePenalty = 50000; // Deduct 50,000 if late

        $attendance = Attendance::where('employee_nip', $request->nip)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            // Check-in
            Attendance::create([
                'employee_nip' => $request->nip,
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

            // Aturan jam 16:00 (Hanya untuk Out)
            if (Carbon::now()->format('H:i') < '16:00') {
                return response()->json([
                    'success' => false,
                    'message' => 'Absen keluar hanya bisa dilakukan setelah jam 16:00.'
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
        $employee = Employee::where('user_id', $user->user_id)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $query = Attendance::where('employee_nip', $employee->nip)
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
        $employee = Employee::where('user_id', $user->user_id)->first();
        
        if (!$employee) {
            return back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')->store('public/permission_proofs');
        }

        // Check if attendance already exists for date
        $exists = Attendance::where('employee_nip', $employee->nip)
            ->where('date', $request->date)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Anda sudah melakukan absensi atau mengajukan izin pada tanggal tersebut.');
        }

        Attendance::create([
            'employee_nip' => $employee->nip,
            'date' => $request->date,
            'status' => $request->status, // Permission
            'description' => $request->description,
            'proof_file' => $proofPath,
            // Time in/out null for Permission
        ]);

        return redirect()->route('attendance.history')->with('success', 'Pengajuan izin berhasil disimpan.');
    }
}
