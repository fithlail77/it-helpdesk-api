<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WebAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('web_user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }
        return $next($request);
    }
}
