<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payroll;
use App\PayrollDetail;
use App\PayrollComponent;
use App\Attendance;
use App\Employee;

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
        if ($user->role->name == 'karyawan') {
             return $this->myPayroll();
        }

        $payrolls = Payroll::orderBy('period_year', 'asc')->orderBy('period_month', 'asc')->get();
        return view('payroll.index', compact('payrolls'));
    }

    /**
     * Show the form for creating a new resource.
     * Admin Only
     */
    public function create()
    {
        if (auth()->user()->role->name != 'admin') {
            return abort(403, 'Akses Ditolak. Hanya Admin yang dapat membuat periode gaji.');
        }
        return view('payroll.create');
    }

    /**
     * Store a newly created resource in storage.
     * Admin Only
     */
    public function store(Request $request)
    {
        if (auth()->user()->role->name != 'admin') {
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
        $userRole = auth()->user()->role->name;
        if ($userRole == 'karyawan') {
             return abort(403);
        }

        $payroll = Payroll::with(['details.employee'])->findOrFail($id);
        return view('payroll.show', compact('payroll'));
    }

    // HRD Manage Details
    public function showDetail($detail_id)
    {
        $detail = PayrollDetail::with(['employee', 'components'])->findOrFail($detail_id);
        
        $user = auth()->user();
        
        // Karyawan can only view their OWN validation
        if ($user->role->name == 'karyawan') {
            if ($detail->nip != $user->employee->nip) {
                return abort(403, 'Anda tidak berhak melihat slip gaji orang lain.');
            }
            return view('payroll.payslip_karyawan', compact('detail'));
        }

        return view('payroll.detail', compact('detail'));
    }

    // Admin Only
    public function generate($id)
    {
        if (auth()->user()->role->name != 'admin') {
            return abort(403, 'Hanya Admin yang dapat men-generate data gaji.');
        }

        $payroll = Payroll::findOrFail($id);
        $employees = Employee::all();

        foreach ($employees as $employee) {
            $exists = PayrollDetail::where('payroll_id', $id)->where('nip', $employee->nip)->first();
            if (!$exists) {
                // Initialize Payroll Detail
                $detail = PayrollDetail::create([
                    'payroll_id' => $id,
                    'nip' => $employee->nip,
                    'basic_salary' => 0, 
                    'total_allowance' => 0,
                    'total_deduction' => 0,
                    'total_salary' => 0,
                ]);

                // Calculate Late Deduction Automatically
                $this->calculateLateDeductionForPeriod($employee->nip, $payroll->period_month, $payroll->period_year, $detail);
            }
        }

        return redirect()->route('payroll.show', $id)->with('success', 'Data gaji karyawan berhasil digenerate.');
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
            $penaltyPerLate = 50000; // Contoh: 50rb per terlambat
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
        if (auth()->user()->role->name != 'hrd') {
            return abort(403, 'Hanya HRD yang dapat mengelola komponen gaji.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:allowance,deduction',
            'amount' => 'required|numeric|min:0',
        ]);

        $detail = PayrollDetail::findOrFail($detail_id);
        
        PayrollComponent::create([
            'payroll_detail_id' => $detail_id,
            'name' => $request->name,
            'type' => $request->type,
            'amount' => $request->amount,
        ]);

        // Update totals in detail
        if ($request->type == 'allowance') {
            $detail->total_allowance += $request->amount;
        } else {
            $detail->total_deduction += $request->amount;
        }
        
        $detail->total_salary = ($detail->basic_salary + $detail->total_allowance) - $detail->total_deduction;
        $detail->save();

        return back()->with('success', 'Komponen gaji berhasil ditambahkan.');
    }

    // HRD Only
    public function updateBasicSalary(Request $request, $detail_id)
    {
        if (auth()->user()->role->name != 'hrd') {
            return abort(403);
        }

        $request->validate(['basic_salary' => 'required|numeric|min:0']);
        
        $detail = \App\PayrollDetail::findOrFail($detail_id);
        $detail->basic_salary = $request->basic_salary;
        $detail->total_salary = ($detail->basic_salary + $detail->total_allowance) - $detail->total_deduction;
        $detail->save();

        return back()->with('success', 'Gaji pokok berhasil diperbarui.');
    }

    // HRD Only
    public function deleteComponent($detail_id, $component_id)
    {
        if (auth()->user()->role->name != 'hrd') { return abort(403); }

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
        if (auth()->user()->role->name != 'hrd') { return abort(403); }
        $detail = \App\PayrollDetail::with('employee')->findOrFail($detail_id);
        $component = \App\PayrollComponent::where('payroll_detail_id', $detail_id)
                                    ->where('payroll_component_id', $component_id)
                                    ->firstOrFail();
        
        return view('payroll.edit_component', compact('detail', 'component'));
    }

    // HRD Only (Update)
    public function updateComponent(Request $request, $detail_id, $component_id)
    {
        if (auth()->user()->role->name != 'hrd') { return abort(403); }

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:allowance,deduction',
            'amount' => 'required|numeric|min:0',
        ]);

        $detail = \App\PayrollDetail::findOrFail($detail_id);
        $component = \App\PayrollComponent::where('payroll_detail_id', $detail_id)
                                    ->where('payroll_component_id', $component_id)
                                    ->firstOrFail();

        // Revert old amounts from detail totals
        if ($component->type == 'allowance') {
            $detail->total_allowance -= $component->amount;
        } else {
            $detail->total_deduction -= $component->amount;
        }

        // Update component
        $component->update([
            'name' => $request->name,
            'type' => $request->type,
            'amount' => $request->amount,
        ]);

        // Add new amounts to detail totals
        if ($request->type == 'allowance') {
            $detail->total_allowance += $request->amount;
        } else {
            $detail->total_deduction += $request->amount;
        }

        // Recalculate total salary
        $detail->total_salary = ($detail->basic_salary + $detail->total_allowance) - $detail->total_deduction;
        $detail->save();

        return redirect()->route('payroll.detail', $detail_id)->with('success', 'Komponen gaji berhasil diperbarui.');
    }

    // Admin Only
    public function edit($id)
    {
        if (auth()->user()->role->name != 'admin') { return abort(403); }
        $editpayroll = Payroll::findOrFail($id);
        return view('payroll.edit', compact('editpayroll'));
    }

    // Admin Only
    public function update(Request $request, $id)
    {
        if (auth()->user()->role->name != 'admin') { return abort(403); }
        $request->validate([
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2000|max:2100',
            'status' => 'required|in:calculated,paid,approved',
        ]);

        $update = Payroll::findOrFail($id);
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
        if (auth()->user()->role->name != 'admin') { return abort(403); }
        Payroll::where('payroll_id', $id)->delete();
        return redirect()->route('payroll.index');
    }

    // Karyawan View
    public function myPayroll()
    {
        $user = auth()->user();
        if (!$user->employee) {
            return redirect('dashboard')->with('error', 'Data karyawan tidak ditemukan.');
        }

        // Get All Payroll Details for this employee
        $payrollDetails = \App\PayrollDetail::with(['payroll'])
            ->where('nip', $user->employee->nip)
            ->whereHas('payroll', function($q) {
                // Optional: Only show Paid or Approved payrolls?
                // $q->where('status', 'paid');
            })
            ->orderBy('payroll_id', 'desc') // Simplified ordering by ID assuming newer ID = newer date
            ->get();
            
        return view('payroll.my_index', compact('payrollDetails'));
    }
}
