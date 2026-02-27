<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Mail\VerifyEmailCode;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $roles = Role::all();
        if ($roles->isEmpty()) {
            // Create a default role if none exists so registration doesn't fail
            Role::create(['name' => 'Employee', 'description' => 'Default Employee Role']);
            $roles = Role::all();
        }
        return view('auth.register', compact('roles'));
    }

    public function register(Request $request)
    {
        // If user exists but is not verified, delete it to allow re-registration
        $existingUnverifiedUser = User::where('email', $request->email)->whereNull('email_verified_at')->first();
        if ($existingUnverifiedUser) {
            $existingUnverifiedUser->delete();
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles_id' => ['required', 'exists:roles,roles_id'],
        ]);

        $verification_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store registration data in session
        $registration_data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'roles_id' => $request->roles_id,
            'verification_code' => $verification_code,
        ];

        session(['registration_temp' => $registration_data]);

        try {
            // Send email
            Mail::to($request->email)->send(new VerifyEmailCode($verification_code));
        } catch (\Exception $e) {
            // If mail fails, we can still proceed to verification page if we want,
            // but for debugging purposes, let's show the error if in local
            return back()->withInput()->withErrors(['email' => 'Gagal mengirim email verifikasi. Pastikan pengaturan email sudah benar. Error: ' . $e->getMessage()]);
        }

        return redirect()->route('verification.notice', ['email' => $request->email])
            ->with('success', 'Silahkan cek email Anda untuk mendapatkan kode verifikasi.');
    }
}
