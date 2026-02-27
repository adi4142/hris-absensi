<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleAccess
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
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        $role = $user->role ? strtolower($user->role->name) : '';
        $routeName = $request->route() ? $request->route()->getName() : '';

        // Allow logout for everyone
        if ($routeName === 'logout') {
            return $next($request);
        }

        // Superadmin: Absolute Full Access
        if ($role === 'superadmin') {
            return $next($request);
        }

        // HRD (Staff Operasional): Akses penuh kecuali manajemen User dan Role
        if ($role === 'hrd' || $role === 'admin') {
            // Proteksi Manajemen Role
            if ($routeName && strpos($routeName, 'role.') === 0) {
                return abort(403, 'Hanya Superadmin yang dapat mengelola hak akses role.');
            }
            
            // Proteksi Manajemen User
            $restrictedUserRoutes = ['user.create', 'user.store', 'user.edit', 'user.update', 'user.destroy'];
            if ($routeName && in_array($routeName, $restrictedUserRoutes)) {
                return abort(403, 'Hanya Superadmin yang dapat mengelola akun pengguna.');
            }

            // Proteksi Manajemen Divisi, Departement, Jabatan (Read-only)
            $readOnlyModules = ['division', 'departement', 'position'];
            foreach ($readOnlyModules as $module) {
                $restrictedModuleRoutes = ["$module.create", "$module.store", "$module.edit", "$module.update", "$module.destroy"];
                if ($routeName && in_array($routeName, $restrictedModuleRoutes)) {
                    return abort(403, 'Hanya Superadmin yang dapat mengelola data ' . $module . '. Anda hanya memiliki hak akses lihat.');
                }
            }

            // Proteksi Payroll (Hanya boleh lihat daftar dan detail)
            if ($routeName && strpos($routeName, 'payroll.') === 0) {
                $allowedPayrollRoutes = ['payroll.index', 'payroll.show', 'payroll.detail', 'payroll.download'];
                if (!in_array($routeName, $allowedPayrollRoutes)) {
                    return abort(403, 'Hanya Superadmin yang dapat memproses atau mengubah data penggajian.');
                }
            }

            return $next($request);
        }

        // Karyawan: Only 'absensi' (attendance)
        if ($role === 'karyawan') {
             // Check if route name starts with 'attendance.'
             // Check if route name starts with 'attendance.' or is 'dashboard'
             if ($routeName && (strpos($routeName, 'attendance.') === 0 || $routeName === 'dashboard' || 
             $routeName === 'payroll.index' || $routeName === 'payroll.detail' || $routeName === 'profile.index' || 
             $routeName === 'payroll.download' || $routeName === 'profile.update' || strpos($routeName, 'leave.') === 0)) {
                 return $next($request);
             }
             return abort(403, 'Karyawan hanya memiliki akses ke menu Absensi dan Cuti.');
        }

        // Tamu: Only apply and view status
        if ($role === 'tamu') {
            // Allowed: jobapplicant, jobvacancie (view), selectionapplicant (status?)
            // We allow these prefixes
            $allowedPrefixes = ['jobvacancie.', 'jobapplication.', 'selection.'];
            $isAllowed = false;
            
            if ($routeName) {
                foreach ($allowedPrefixes as $prefix) {
                    if (strpos($routeName, $prefix) === 0) {
                        $isAllowed = true;
                        break;
                    }
                }
            }
            
            if ($isAllowed) {
                return $next($request);
            }
            
            return abort(403, 'Tamu hanya dapat melamar dan melihat status lamaran.');
        }

        // If no role matched or allowed (Fallback)
        return abort(403, 'Role tidak dikenali atau tidak memiliki akses.');
    }
}
