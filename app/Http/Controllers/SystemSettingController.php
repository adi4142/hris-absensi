<?php

namespace App\Http\Controllers;

use App\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    /**
     * Menampilkan halaman pengaturan sistem
     */
    public function index()
    {
        $settings = SystemSetting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Menyimpan perubahan pengaturan sistem
     */
    public function update(Request $request)
    {
        $request->validate([
            'work_start_time' => 'required',
            'work_end_time' => 'required',
            'attendance_radius' => 'required|numeric',
            'office_latitude' => 'required',
            'office_longitude' => 'required',
            'late_deduction_amount' => 'required|numeric',
        ]);

        $data = $request->except('_token');

        foreach ($data as $key => $value) {
            SystemSetting::set($key, $value);
        }

        return redirect()->back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}
