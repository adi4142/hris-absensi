<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect('login');
        }

        // Cek apakah user memiliki role superadmin
        // Kita bandingkan dengan nama role lowercase untuk konsistensi
        $role = Auth::user()->role ? strtolower(Auth::user()->role->name) : '';

        if ($role === 'superadmin') {
            return $next($request);
        }

        // Jika bukan superadmin, lempar error 403 (Unauthorized)
        return abort(403, 'Anda tidak memiliki hak akses untuk halaman ini. Hanya Superadmin yang diperbolehkan.');
    }
}
