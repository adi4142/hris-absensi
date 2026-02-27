<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ],
            [
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Email harus disertai dengan @.',
                'password.required' => 'Password wajib diisi.',
            ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (!$user->email_verified_at) {
                Auth::logout();
                return back()->with('error', 'Email Anda belum diverifikasi. Silahkan hubungi Admin untuk aktivasi akun.');
            }

            $request->session()->regenerate();
            // Check if user has role and get the role name
            $role = $user->role ? strtolower($user->role->name) : '';

            if ($role === 'superadmin' || $role === 'admin') {
                return redirect()->route('dashboard');
            } elseif ($role === 'karyawan') {
                return redirect()->route('attendance.dashboard');
            } elseif ($role === 'hrd') {
                return redirect()->route('dashboard');
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Email atau password yang anda masukkan salah.'])
                     ->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('login');
    }
}
