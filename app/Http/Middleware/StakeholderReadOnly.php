<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class StakeholderReadOnly
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
        $user = $request->user();
        
        if ($user && $user->role === 'Stakeholder') {
            // Izinkan GET requests, route logout, serta kelola aspekprogram dan pesan stakeholder
            if (!$request->isMethod('get') 
                && !$request->routeIs('logout') 
                && !$request->routeIs('stakeholder.logout')
                && !$request->routeIs('aspekprogram.*')
                && !$request->routeIs('pesan_stakeholder.*')) {
                return redirect()->back()->with('error', 'Stakeholder hanya memiliki akses baca (read-only).');
            }
        }
        return $next($request);
    }
}
