<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            // Jika user sudah login dan mengakses halaman pengawas login, redirect ke dashboard pengawas
            if ($request->is('pengawas/login')) {
                return redirect()->route('pengawas.index');
            }
            // Default redirect ke home
            return redirect(RouteServiceProvider::HOME);
        }

        return $next($request);
    }
}
