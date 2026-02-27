<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\User;
use App\Mail\ResetPasswordCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    // 1. Show form to input email
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    // 2. Process email and send code
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => ['required','email']],
            [
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Email harus disertai dengan @.',
            ]);
        
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // To prevent user enumeration, we can return success even if email not found, 
            // but for better UX in this context, let's say email not found or send generic message.
            return back()->withErrors(['email' => 'Email tidak ditemukan.'])
                         ->withInput();
        }

        // Generate 6 digit code
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store in password_resets table. 
        // We delete existing tokens for this email first.
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Store plain code as token for simplicity in this verification flow context
        // OR store hashed code. Let's store hashed code for security, 
        // but we need to verify against it later.
        // Actually, since user inputs the code, we should store hash and compare.
        // Wait, standard Laravel password_resets uses token which is long string.
        // We are hijacking this table for our 6-digit code.
        
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => Hash::make($code), 
            'created_at' => Carbon::now()
        ]);

        try {
            Mail::to($user->email)->send(new ResetPasswordCode($code));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Gagal mengirim email: ' . $e->getMessage()]);
        }

        // Redirect to code verification page with email in session
        return redirect()->route('password.verify.show')->with(['email' => $request->email]);
    }

    // 3. Show code input form
    public function showCodeForm()
    {
        $email = old('email') ?? session('email');

        if (!$email) {
            return redirect()->route('password.request');
        }

        return view('auth.passwords.verify', compact('email'));

    }

    // 4. Verify code
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => ['required','email'],
            'code' => ['required','string','size:6'],
        ],
            [
            'code.required' => 'Kode wajib diisi.',
            'code.string' => 'Kode harus berupa angka.',
            'code.size' => 'Kode harus terdiri dari 6 digit.',
            ]
            );

        $record = DB::table('password_resets')->where('email', $request->email)->first();

        if (!$record) {
             return redirect()->route('password.verify.show')->withErrors(['code' => 'Permintaan reset password tidak valid atau kadaluarsa.'])->withInput();
        }

        // Check expiration (e.g. 60 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            return redirect()->route('password.request')->withErrors(['email' => 'Kode kadaluarsa, silahkan minta kode baru.']);
        }

        if (!Hash::check($request->code, $record->token)) {
            return redirect()->route('password.verify.show')->withErrors(['code' => 'Kode verifikasi salah.'])
                         ->withInput();
        }

        // Code is correct. 
        // We can't log them in yet, we need them to reset password.
        // We need to pass a secure token to the reset form so they can't bypass verification.
        // For simplicity, let's keep the valid record in DB and use a session 'verified_email' 
        // or redirect to a signed URL. 
        // Let's use session 'reset_email' which is set ONLY after successful verification.
        
        session(['reset_email' => $request->email]);

        return redirect()->route('password.reset.form');
    }

    // 5. Show reset password form
    public function showResetForm()
    {
        if (!session('reset_email')) {
             return redirect()->route('password.request');
        }
        return view('auth.passwords.reset', ['email' => session('reset_email')]);
    }

    // 6. Reset password based on session
    public function reset(Request $request)
    {
        if (!session('reset_email')) {
             return redirect()->route('password.request');
        }

        $request->validate([
            'password' => ['required','confirmed','min:8'],
        ],[
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $email = session('reset_email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request')->withErrors(['email' => 'User tidak ditemukan.']);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete reset token
        DB::table('password_resets')->where('email', $email)->delete();
        
        // Clear session
        session()->forget('reset_email');

        return redirect()->route('login')->with('success', 'Password berhasil diubah. Silahkan login dengan password baru.');
    }
}
