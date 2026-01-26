<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class CheckSuperadminOrStakeholder
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user && ($user->role === 'Super Admin' || $user->role === 'Stakeholder' || $user->role === 'Admin')) {
            return $next($request);
        }
        return redirect('/'); // Ganti dengan route lain sesuai kebutuhan

    }
}
