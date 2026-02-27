<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payroll;
use App\PayrollDetail;
use App\PayrollComponent;
use App\Attendance;
use App\User;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $role = strtolower($user->role->name);

        if ($role == 'karyawan') {
             return $this->myPayroll();
        }

        // Superadmin and Admin can see all, but here we can add filters if needed
        $payrolls = Payroll::orderBy('period_year', 'desc')->orderBy('period_month', 'desc')->get();
        return view('payroll.index', compact('payrolls'));
    }

    /**
     * Show the form for creating a new resource.
     * Admin Only
     */
    public function create()
    {
        $role = strtolower(auth()->user()->role->name);
        if ($role != 'hrd' && $role != 'admin' && $role != 'superadmin') {
            return abort(403, 'Akses Ditolak. Hanya HRD/Admin/Superadmin yang dapat membuat periode gaji.');
        }
        return view('payroll.create');
    }

    /**
     * Store a newly created resource in storage.
     * Admin Only
     */
    public function store(Request $request)
    {
        $role = strtolower(auth()->user()->role->name);
        if ($role != 'hrd' && $role != 'admin' && $role != 'superadmin') {
            return abort(403);
        }

        $request->validate([
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2000|max:2100',
            'status' => 'required|in:calculated,paid,approved',
        ]);

        Payroll::create([
            'period_month' => $request->period_month,
            'period_year' => $request->period_year,
            'status' => $request->status,
        ]);

        return redirect()->route('payroll.index')->with('success', 'Periode gaji berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     * Admin & HRD can view.
     */
    public function show($id)
    {
        $role = strtolower(auth()->user()->role->name);
        if ($role == 'karyawan') {
             return abort(403);
        }

        $payroll = Payroll::findOrFail($id);
        
        // Auto-sync payroll details if not locked
        if (!$payroll->is_locked || $role == 'superadmin') {
            $employees = User::whereNotNull('nip')->get();
            $activeNips = $employees->pluck('nip')->toArray();

            // 1. Delete details for employees that no longer exist
            PayrollDetail::where('payroll_id', $id)
                         ->whereNotIn('nip', $activeNips)
                         ->delete();

            // 2. Add or update details for existing employees
            foreach ($employees as $employee) {
                if (empty($employee->nip)) continue;

                $detail = PayrollDetail::where('payroll_id', $id)->where('nip', $employee->nip)->first();
                
                if (!$detail) {
                    $detail = PayrollDetail::create([
                        'payroll_id' => $id,
                        'nip' => $employee->nip,
                        'basic_salary' => $employee->basic_salary ?? 0, 
                        'total_allowance' => 0,
                        'total_deduction' => 0,
                        'total_salary' => $employee->basic_salary ?? 0,
                    ]);
                    $this->calculateLateDeductionForPeriod($employee->nip, $payroll->period_month, $payroll->period_year, $detail);
                } else {
                    $detail->basic_salary = $employee->basic_salary ?? 0;
                    $detail->total_salary = ($detail->basic_salary + $detail->total_allowance) - $detail->total_deduction;
                    $detail->save();
                }
            }
        }

        // Reload data after sync
        $payroll = Payroll::with(['details.employee'])->findOrFail($id);
        return view('payroll.show', compact('payroll'));
    }

    // HRD Manage Details
    public function showDetail($detail_id)
    {
        $detail = PayrollDetail::with(['employee', 'components'])->findOrFail($detail_id);
        
        $user = auth()->user();
        
        // Karyawan can only view their OWN validation
        if (strtolower($user->role->name) == 'karyawan') {
            if ($detail->nip != $user->nip) {
                return abort(403, 'Anda tidak berhak melihat slip gaji orang lain.');
            }
            return view('payroll.payslip_karyawan', compact('detail'));
        }

        return view('payroll.detail', compact('detail'));
    }

    // Admin Only
    public function generate($id)
    {
        $role = strtolower(auth()->user()->role->name);
        if ($role != 'hrd' && $role != 'admin' && $role != 'superadmin') {
            return abort(403, 'Hanya HRD/Admin/Superadmin yang dapat men-generate data gaji.');
        }

        $payroll = Payroll::findOrFail($id);
        
        // Proteksi: HRD tidak boleh generate jika sudah dikunci superadmin
        if ($payroll->is_locked && $role != 'superadmin') {
            return abort(403, 'Periode gaji ini sudah dikunci oleh Superadmin.');
        }
        $employees = User::whereNotNull('nip')->get();

        foreach ($employees as $employee) {
            // Pastikan nip tidak kosong
            if (empty($employee->nip)) continue;

            $detail = PayrollDetail::where('payroll_id', $id)->where('nip', $employee->nip)->first();
            
            if (!$detail) {
                // Initialize Payroll Detail for new entry
                $detail = PayrollDetail::create([
                    'payroll_id' => $id,
                    'nip' => $employee->nip,
                    'basic_salary' => $employee->basic_salary ?? 0, 
                    'total_allowance' => 0,
                    'total_deduction' => 0,
                    'total_salary' => $employee->basic_salary ?? 0,
                ]);

                // Calculate Late Deduction Automatically for NEW record
                $this->calculateLateDeductionForPeriod($employee->nip, $payroll->period_month, $payroll->period_year, $detail);
            } else {
                // Update basic salary for EXISTING record if not locked
                if (!$payroll->is_locked || $role == 'superadmin') {
                    $detail->basic_salary = $employee->basic_salary ?? 0;
                    
                    // Recalculate total salary based on existing components
                    $detail->total_salary = ($detail->basic_salary + $detail->total_allowance) - $detail->total_deduction;
                    $detail->save();

                    // Optional: If you want to recalculate late deductions every time, you'd need to clear old ones first.
                    // For now, only basic salary sync is requested.
                }
            }
        }

        return redirect()->route('payroll.show', $id)->with('success', 'Data gaji berhasil digenerate / disinkronkan dengan master data karyawan.');
    }

    private function calculateLateDeductionForPeriod($nip, $month, $year, $detail)
    {
        // Hitung jumlah terlambat bulan ini
        $lateCount = Attendance::where('employee_nip', $nip)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('status', 'Late')
            ->count();
        
        if ($lateCount > 0) {
            // Ambil besaran potongan dari pengaturan sistem
            $penaltyPerLate = \App\SystemSetting::get('late_deduction_amount', 5000); 
            $totalPenalty = $lateCount * $penaltyPerLate;

            // Tambahkan sebagai komponen deduction
            PayrollComponent::create([
                'payroll_detail_id' => $detail->payroll_detail_id,
                'name' => 'Potongan Keterlambatan (' . $lateCount . 'x)',
                'type' => 'deduction',
                'amount' => $totalPenalty
            ]);

            // Update Detail Totals
            $detail->total_deduction += $totalPenalty;
            $detail->total_salary = ($detail->basic_salary + $detail->total_allowance) - $detail->total_deduction;
            $detail->save();
        }
    }

    // HRD Only
    public function addComponent(Request $request, $detail_id)
    {
        $user = auth()->user();
        $role = strtolower($user->role->name);
        
        if ($role != 'superadmin') {
            return abort(403, 'Hanya Superadmin yang dapat menambah komponen gaji.');
        }

        $detail = PayrollDetail::with('payroll')->findOrFail($detail_id);
        
        // Proteksi: HRD tidak boleh ubah jika sudah dikunci superadmin
        if ($detail->payroll->is_locked && $role != 'superadmin') {
            return abort(403, 'Data gaji ini sudah dikunci oleh Superadmin.');
        }

        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'calculation_type' => 'required|in:fixed,percentage',
            'amount' => 'required|numeric'
        ]);

        $calculationValue = $request->amount;
        $finalAmount = $calculationValue;

        if ($request->calculation_type == 'percentage') {
            $finalAmount = ($calculationValue / 100) * $detail->basic_salary;
        }
        
        PayrollComponent::create([
            'payroll_detail_id' => $detail_id,
            'name' => $request->name,
            'type' => $request->type,
            'calculation_type' => $request->calculation_type,
            'calculation_value' => $calculationValue,
            'amount' => $finalAmount,
        ]);

        // Update totals in detail
        if ($request->type == 'allowance') {
            $detail->total_allowance += $finalAmount;
        } else {
            $detail->total_deduction += $finalAmount;
        }
        
        $detail->total_salary = ($detail->basic_salary + $detail->total_allowance) - $detail->total_deduction;
        $detail->save();

        return back()->with('success', 'Komponen gaji berhasil ditambahkan.');
    }

    // HRD Only
    public function updateBasicSalary(Request $request, $detail_id)
    {
        $user = auth()->user();
        $role = strtolower($user->role->name);

        if ($role != 'superadmin') {
            return abort(403, 'Hanya Superadmin yang dapat mengubah gaji pokok.');
        }

        $detail = \App\PayrollDetail::with(['payroll', 'components'])->findOrFail($detail_id);

        if ($detail->payroll->is_locked && $role != 'superadmin') {
            return abort(403, 'Data gaji ini sudah dikunci oleh Superadmin.');
        }

        $detail->basic_salary = $request->basic_salary;

        // Recalculate all components if they are percentage-based
        $totalAllowance = 0;
        $totalDeduction = 0;

        foreach ($detail->components as $component) {
            if ($component->calculation_type == 'percentage') {
                $component->amount = ($component->calculation_value / 100) * $detail->basic_salary;
                $component->save();
            }

            if ($component->type == 'allowance') {
                $totalAllowance += $component->amount;
            } else {
                $totalDeduction += $component->amount;
            }
        }

        $detail->total_allowance = $totalAllowance;
        $detail->total_deduction = $totalDeduction;
        $detail->total_salary = ($detail->basic_salary + $detail->total_allowance) - $detail->total_deduction;
        $detail->save();

        return back()->with('success', 'Gaji pokok diperbarui dan komponen persenan telah dihitung ulang.');
    }

    // HRD Only
    public function deleteComponent($detail_id, $component_id)
    {
        $user = auth()->user();
        $role = strtolower($user->role->name);

        if ($role != 'superadmin') { return abort(403, 'Hanya Superadmin yang dapat menghapus komponen gaji.'); }

        $detail = \App\PayrollDetail::with('payroll')->findOrFail($detail_id);

        if ($detail->payroll->is_locked && $role != 'superadmin') {
            return abort(403, 'Data gaji ini sudah dikunci oleh Superadmin.');
        }

        $component = \App\PayrollComponent::where('payroll_detail_id', $detail_id)
                                    ->where('payroll_component_id', $component_id)
                                    ->firstOrFail();
            
        $detail = \App\PayrollDetail::findOrFail($detail_id);
        
        // Decrease totals before deleting
        if ($component->type == 'allowance') {
            $detail->total_allowance -= $component->amount;
        } else {
            $detail->total_deduction -= $component->amount;
        }
        
        $component->delete();

        // Recalculate total salary
        $detail->total_salary = ($detail->basic_salary + $detail->total_allowance) - $detail->total_deduction;
        $detail->save();

        return back()->with('success', 'Komponen gaji berhasil dihapus.');
    }

    // HRD Only (View Edit)
    public function editComponent($detail_id, $component_id)
    {
        $role = strtolower(auth()->user()->role->name);
        if ($role != 'superadmin') { return abort(403, 'Hanya Superadmin yang dapat mengedit komponen gaji.'); }
        $detail = \App\PayrollDetail::with('employee')->findOrFail($detail_id);
        $component = \App\PayrollComponent::where('payroll_detail_id', $detail_id)
                                    ->where('payroll_component_id', $component_id)
                                    ->firstOrFail();
        
        return view('payroll.edit_component', compact('detail', 'component'));
    }

    // HRD Only (Update)
    public function updateComponent(Request $request, $detail_id, $component_id)
    {
        $user = auth()->user();
        $role = strtolower($user->role->name);

        if ($role != 'superadmin') { return abort(403, 'Hanya Superadmin yang dapat memperbarui komponen gaji.'); }

        $detail = \App\PayrollDetail::with('payroll')->findOrFail($detail_id);

        if ($detail->payroll->is_locked && $role != 'superadmin') {
            return abort(403, 'Data gaji ini sudah dikunci oleh Superadmin.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:allowance,deduction',
            'calculation_type' => 'required|in:fixed,percentage',
            'amount' => 'required|numeric|min:0',
        ]);

        $component = \App\PayrollComponent::where('payroll_detail_id', $detail_id)
                                    ->where('payroll_component_id', $component_id)
                                    ->firstOrFail();

        // Revert old amounts from detail totals
        if ($component->type == 'allowance') {
            $detail->total_allowance -= $component->amount;
        } else {
            $detail->total_deduction -= $component->amount;
        }

        $calculationValue = $request->amount;
        $finalAmount = $calculationValue;

        if ($request->calculation_type == 'percentage') {
            $finalAmount = ($calculationValue / 100) * $detail->basic_salary;
        }

        // Update component
        $component->update([
            'name' => $request->name,
            'type' => $request->type,
            'calculation_type' => $request->calculation_type,
            'calculation_value' => $calculationValue,
            'amount' => $finalAmount,
        ]);

        // Add new amounts to detail totals
        if ($request->type == 'allowance') {
            $detail->total_allowance += $finalAmount;
        } else {
            $detail->total_deduction += $finalAmount;
        }

        // Recalculate total salary
        $detail->total_salary = ($detail->basic_salary + $detail->total_allowance) - $detail->total_deduction;
        $detail->save();

        return redirect()->route('payroll.detail', $detail_id)->with('success', 'Komponen gaji berhasil diperbarui.');
    }

    // Admin Only
    public function edit($id)
    {
        $role = strtolower(auth()->user()->role->name);
        if ($role != 'hrd' && $role != 'admin' && $role != 'superadmin') { return abort(403); }
        $editpayroll = Payroll::findOrFail($id);
        return view('payroll.edit', compact('editpayroll'));
    }

    // Admin Only
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $role = strtolower($user->role->name);

        if ($role != 'hrd' && $role != 'admin' && $role != 'superadmin') { return abort(403); }
        
        $update = Payroll::findOrFail($id);

        if ($update->is_locked && $role != 'superadmin') {
            return abort(403, 'Periode gaji ini sudah dikunci oleh Superadmin.');
        }
        $update->update([
            'period_month' => $request->period_month,
            'period_year' => $request->period_year,
            'status' => $request->status,
        ]);

        return redirect()->route('payroll.index');
    }

    // Admin Only
    public function destroy($id)
    {
        $user = auth()->user();
        $role = strtolower($user->role->name);
        
        if ($role != 'hrd' && $role != 'admin' && $role != 'superadmin') { return abort(403); }
        
        $payroll = Payroll::findOrFail($id);
        
        // HRD TIDAK BOLEH menghapus payroll yang sudah dikunci
        if ($payroll->is_locked && $role != 'superadmin') {
            return abort(403, 'Anda tidak dapat menghapus periode gaji yang sudah dikunci oleh Superadmin.');
        }

        $payroll->delete();
        return redirect()->route('payroll.index')->with('success', 'Periode gaji berhasil dihapus.');
    }

    public function destroyDetail($id)
    {
        $user = auth()->user();
        $role = strtolower($user->role->name);
        
        if ($role != 'hrd' && $role != 'admin' && $role != 'superadmin') { return abort(403); }
        
        $detail = PayrollDetail::findOrFail($id);
        
        // HRD TIDAK BOLEH menghapus detail payroll yang sudah dikunci
        if ($detail->payroll->is_locked && $role != 'superadmin') {
            return abort(403, 'Anda tidak dapat menghapus detail payroll yang sudah dikunci oleh Superadmin.');
        }

        $detail->delete();
        return redirect()->route('payroll.show', $detail->payroll_id)->with('success', 'Detail payroll berhasil dihapus.');
    }

    /**
     * Kunci periode payroll (Superadmin Only)
     */
    public function lock($id)
    {
        if (strtolower(auth()->user()->role->name) != 'superadmin') {
            return abort(403, 'Hanya Superadmin yang dapat mengunci periode gaji.');
        }

        $payroll = Payroll::findOrFail($id);
        $payroll->update(['is_locked' => true]);

        return back()->with('success', 'Periode gaji berhasil dikunci. HRD tidak dapat mengubah data ini lagi.');
    }

    /**
     * Buka kembali periode payroll (Superadmin Only)
     */
    public function unlock($id)
    {
        if (strtolower(auth()->user()->role->name) != 'superadmin') {
            return abort(403, 'Hanya Superadmin yang dapat membuka kunci periode gaji.');
        }

        $payroll = Payroll::findOrFail($id);
        $payroll->update(['is_locked' => false]);

        return back()->with('success', 'Kunci periode gaji berhasil dibuka.');
    }

    // Karyawan View
    public function myPayroll()
    {
        $user = auth()->user();
        if (!$user->nip) {
            return redirect('dashboard')->with('error', 'Data karyawan tidak ditemukan (NIP kosong).');
        }

        // Get All Payroll Details for this employee
        $payrollDetails = \App\PayrollDetail::with(['payroll'])
            ->where('nip', $user->nip)
            ->whereHas('payroll', function($q) {
                // Optional: Only show Paid or Approved payrolls?
                // $q->where('status', 'paid');
            })
            ->orderBy('payroll_id', 'desc') // Simplified ordering by ID assuming newer ID = newer date
            ->get();
            
        return view('payroll.my_index', compact('payrollDetails'));
    }

    public function downloadPdf($id)
    {
        $detail = PayrollDetail::with(['employee', 'payroll', 'components'])->findOrFail($id);
        
        $user = auth()->user();
        if (strtolower($user->role->name) == 'karyawan') {
            if ($detail->nip != $user->nip) {
                return abort(403, 'Anda tidak berhak mengunduh slip gaji orang lain.');
            }
        }

        $pdf = Pdf::loadView('payroll.pdf', compact('detail'));

        return $pdf->download('slip-gaji-' . $detail->employee->name . '.pdf');
    }
}
