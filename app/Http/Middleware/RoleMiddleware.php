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
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        // dd($user->role);

        if (! $user) {
            return redirect()->route('login')->with('error', 'Harap Login Terlebih Dahulu');
        }

        // Check Role
        if (! in_array($user->role->name, $roles)) {
            abort(403, 'Anda tidak dapat mengakses halaman ini');
        }

        // Jika karyawan tidak aktif
        if (!$user->employee() || $user->employee->status !== 'active') {
            Auth::logout();

            return redirect()->route('login')->with('error', 'Akun anda sudah tidak aktif');
        }

        return $next($request);
    }
}
