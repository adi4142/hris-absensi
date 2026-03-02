<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\BasicSalary;

class BasicSalaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $userRole = strtolower(auth()->user()->role->name ?? '');
        if ($userRole !== 'superadmin' && $userRole !== 'hrd' && $userRole !== 'admin') {
            return abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $salaries = BasicSalary::with('user')->orderBy('effective_date', 'desc')->get();
        $users = User::whereNotNull('nip')->get();
        return view('salary.index', compact('salaries', 'users'));
    }

    public function store(Request $request)
    {
        $userRole = strtolower(auth()->user()->role->name ?? '');
        if ($userRole !== 'superadmin' && $userRole !== 'hrd' && $userRole !== 'admin') {
            return abort(403);
        }

        $request->validate([
            'user_nip' => 'required|exists:users,nip',
            'basic_salary' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
        ], [
            'user_nip.required' => 'Karyawan harus dipilih.',
            'basic_salary.required' => 'Gaji pokok wajib diisi.',
            'effective_date.required' => 'Tanggal berlaku wajib diisi.',
        ]);

        BasicSalary::create([
            'user_nip' => $request->user_nip,
            'basic_salary' => $request->basic_salary,
            'effective_date' => $request->effective_date,
        ]);

        // Update the cached basic_salary in users table for system compatibility
        $user = User::find($request->user_nip);
        if ($user) {
            $user->basic_salary = $request->basic_salary;
            $user->save();
        }

        return redirect()->route('salary.index')->with('success', 'Gaji pokok berhasil diatur.');
    }

    public function destroy($id)
    {
        $userRole = strtolower(auth()->user()->role->name ?? '');
        if ($userRole !== 'superadmin') {
            return abort(403, 'Hanya Superadmin yang dapat menghapus riwayat gaji.');
        }

        $salary = BasicSalary::findOrFail($id);
        $salary->delete();
        
        return redirect()->route('salary.index')->with('success', 'Riwayat gaji berhasil dihapus.');
    }
}
