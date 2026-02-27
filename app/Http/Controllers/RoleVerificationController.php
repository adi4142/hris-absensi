<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use Illuminate\Support\Facades\Auth;

class RoleVerificationController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        if ($user->is_role_verified) {
            return redirect()->route('dashboard');
        }
        
        $role = $user->role ? strtolower($user->role->name) : '';
        if (!in_array($role, ['admin', 'hrd'])) {
            return redirect()->route('dashboard');
        }

        return view('auth.role_verification');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'license_code' => 'required',
        ]);

        $user = Auth::user();
        $role = $user->role ? strtolower($user->role->name) : '';
        $code = $request->license_code;

        $isValid = false;

        if ($role === 'admin') {
            if ($code === 'ADMIN2026XYZ') {
                $isValid = true;
            }
        } elseif ($role === 'hrd') {
            if ($code === 'HRD2026ABC') {
                $isValid = true;
            }
        } else {
            // Should not happen due to middleware, but redirect if role doesn't need verification
            return redirect()->route('dashboard');
        }

        if ($isValid) {
            $user->is_role_verified = true;
            $user->save();
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['license_code' => 'Kode verifikasi tidak valid.']);
    }
}
