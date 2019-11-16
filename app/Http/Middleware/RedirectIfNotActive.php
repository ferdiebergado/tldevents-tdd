<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfNotActive
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
        if (!auth()->user()->is_active) {
            session()->flash('error', 'Sorry. Your account has been deactivated.');
            auth()->guard()->logout();
            return redirect()->route('login');
        }
        return $next($request);
    }
}
