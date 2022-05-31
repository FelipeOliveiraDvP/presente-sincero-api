<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HasToken
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

        if (empty($user)) {
            return response()->json(['message' => 'Não autorizado!'], 401);
        }

        $request->merge(['user' => $user]);

        return $next($request);
    }
}
