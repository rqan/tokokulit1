<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user sudah login
        if (!Auth::check() && !$request->session()->get('isLoggedIn')) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();
        $userRole = $user->role ?? $request->session()->get('role', 'pelanggan');

        // Parse format multiple roles seperti 'admin,superadmin' atau argumen berurutan
        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $r) {
                $allowedRoles[] = trim($r);
            }
        }

        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}
