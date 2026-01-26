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
            $user = Auth::guard($guard)->user();
            
            // Jika user sudah login dan mengakses halaman login, redirect ke dashboard yang sesuai
            if ($request->is('pengawas/login') || $request->is('stakeholder/login') || $request->is('login')) {
                if ($user->role == 'Pengawas') {
                return redirect()->route('pengawas.index');
                }
                return redirect(RouteServiceProvider::HOME);
            }
            
            // Default redirect ke home
            return redirect(RouteServiceProvider::HOME);
        }

        return $next($request);
    }
}
