<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakhal Login Terlebih Dahulu');
        }

        if (!in_array($user->role->name, $roles)) {
            abort(403, 'Anda tidak dapat mengakses halaman ini');
        }
        return $next($request);
    }
}
