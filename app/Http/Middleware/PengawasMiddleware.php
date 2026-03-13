<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class PengawasMiddleware
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
        // Pengecualian untuk route login
        if ($request->is('pengawas/login') || $request->is('pengawas/logout')) {
            return $next($request);
        }

        // Cek apakah pengguna terautentikasi dan memiliki peran 'pengawas'
        if(Auth::check() && strtolower(Auth::user()->role) == "pengawas") {
            return $next($request);
        } else {
            return redirect('/pengawas/login');
        }
    }
}
