<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureRoleVerified
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
        if (! $request->user()) {
            return $next($request);
        }

        $user = $request->user();
        
        // Skip if email not verified (let standard verification handle that if used)
        if (! $user->hasVerifiedEmail()) {
            return $next($request);
        }

        $role = $user->role ? strtolower($user->role->name) : '';
        
        // Roles that require additional verification
        $restrictedRoles = ['admin', 'hrd'];

        if (in_array($role, $restrictedRoles)) {
            if (! $user->is_role_verified) {
                // If user is not role verified, redirect to verification page
                // unless they are already there or logging out
                if (! $request->is('role/verify') && ! $request->is('logout')) {
                    return redirect()->route('role.verification.notice');
                }
            } else {
                // If user IS role verified, but tries to access verification page, redirect to dashboard
                if ($request->is('role/verify')) {
                    return redirect()->route('dashboard');
                }
            }
        }

        return $next($request);
    }
}
