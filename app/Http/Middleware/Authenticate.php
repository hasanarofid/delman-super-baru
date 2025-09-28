<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // Jika request ke route pengawas, redirect ke login pengawas
            if ($request->is('pengawas*')) {
                return route('pengawas.login');
            }
            // Default redirect ke admin login
            return route('login');
        }
    }
}