<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Permitir todos los métodos GET
        // if ($request->isMethod('get')) {
        //     return $next($request);
        // }

        if (!(Auth::check() && in_array(Auth::user()->role, $roles))) {
            return response()->json(['error' => 'Unauthorized2'], 403);
        }

        return $next($request);
    }

}
