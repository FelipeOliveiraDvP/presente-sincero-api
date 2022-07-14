<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsBlocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth('sanctum')->user();

        if ($user->blocked) {
            return response()->json(['message' => 'Sua conta bloqueada! Entre em contato com o suporte.'], 403);
        }

        return $next($request);
    }
}
