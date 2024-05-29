<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle(Request $request, Closure $next,...$guards)
    {
        $request->headers->set('Accept', 'application/json');
        if ($this->authenticate($request, $guards)) {
            return $next($request);
        }

        return $request->expectsJson()
               ? response()->json(['message' => 'Unauthorized'], 401)
                : redirect()->guest(route('login'));
    }

    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return bool
     */
    protected function authenticate(Request $request, array $guards)
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return true;
            }
        }

        return false;
    }
}
