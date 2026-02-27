<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmailCode;

class VerificationController extends Controller
{
    public function show(Request $request)
    {
        $data = session('registration_temp');
        if (!$data) {
            return redirect()->route('register')->with('error', 'Sesi pendaftaran tidak ditemukan. Silahkan daftar ulang.');
        }

        $email = $data['email'];
        return view('auth.verify-code', compact('email'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $data = session('registration_temp');

        if (!$data) {
            return redirect()->route('register')->with('error', 'Sesi pendaftaran kadaluarsa. Silahkan daftar ulang.');
        }

        if ($data['verification_code'] !== $request->code) {
            return back()->withErrors(['code' => 'Kode verifikasi tidak valid.'])->withInput();
        }

        // Final check if email is already taken (edge case)
        if (User::where('email', $data['email'])->exists()) {
            session()->forget('registration_temp');
            return redirect()->route('register')->with('error', 'Email ini sudah terdaftar. Silahkan gunakan email lain atau login.');
        }

        // Create user in database
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'roles_id' => $data['roles_id'],
            'email_verified_at' => Carbon::now(),
        ]);

        // Determine if user needs additional role verification
        $roleName = $user->role ? strtolower($user->role->name) : '';
        
        // Auto-verify roles that don't need license code
        if ($roleName !== 'admin' && $roleName !== 'hrd') {
            $user->is_role_verified = true;
            $user->save();
        }

        // Clear session
        session()->forget('registration_temp');

        Auth::login($user);

        if ($roleName === 'admin' || $roleName === 'hrd') {
            return redirect()->route('role.verification.notice')
                ->with('info', 'Email Anda telah diverifikasi. Silahkan masukkan kode lisensi untuk mengaktifkan akun ' . strtoupper($roleName) . '.');
        }

        if ($roleName === 'karyawan') {
            return redirect()->route('attendance.dashboard')->with('success', 'Pendaftaran berhasil dan email telah diverifikasi!');
        }

        return redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil dan email telah diverifikasi!');
    }

    public function resend(Request $request)
    {
        $data = session('registration_temp');
        
        if (!$data) {
            return redirect()->route('register')->with('error', 'Sesi pendaftaran tidak ditemukan.');
        }

        $verification_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Update session with new code
        $data['verification_code'] = $verification_code;
        session(['registration_temp' => $data]);

        try {
            Mail::to($data['email'])->send(new VerifyEmailCode($verification_code));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim ulang email. Error: ' . $e->getMessage());
        }

        return back()->with('success', 'Kode verifikasi baru telah dikirim ke email Anda.');
    }
}
