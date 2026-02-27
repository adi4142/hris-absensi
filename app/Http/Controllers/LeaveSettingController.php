<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LeaveSetting;
use App\User;
use Illuminate\Support\Facades\DB;

class LeaveSettingController extends Controller
{
    /**
     * Menampilkan halaman pengaturan cuti (Hanya Superadmin).
     */
    public function index()
    {
        $setting = LeaveSetting::first();
        if (!$setting) {
            $setting = LeaveSetting::create([
                'annual_allowance' => 12,
                'can_carry_over' => false,
                'max_days_per_request' => 12,
            ]);
        }
        return view('superadmin.leave_settings.index', compact('setting'));
    }

    /**
     * Memperbarui pengaturan cuti dan opsional mereset sisa cuti karyawan.
     */
    public function update(Request $request)
    {
        $request->validate([
            'annual_allowance' => 'required|integer|min:0',
            'max_days_per_request' => 'required|integer|min:1',
        ], [
            'annual_allowance.required' => 'Jatah tahunan wajib diisi.',
            'max_days_per_request.required' => 'Maksimal hari per pengajuan wajib diisi.',
        ]);

        $setting = LeaveSetting::first();
        $setting->update([
            'annual_allowance' => $request->annual_allowance,
            'can_carry_over' => $request->has('can_carry_over'),
            'max_days_per_request' => $request->max_days_per_request,
        ]);

        // Jika user mencentang 'Update All Employees', maka update jatah cuti semua karyawan
        if ($request->has('update_employees')) {
            User::whereNotNull('nip')->update([
                'total_jatah_cuti' => $request->annual_allowance,
                // Sisa cuti dihitung ulang: jatah baru - cuti terpakai
                'sisa_cuti' => DB::raw($request->annual_allowance . ' - cuti_terpakai')
            ]);
        }

        return redirect()->back()->with('success', 'Pengaturan cuti berhasil diperbarui.');
    }
}
