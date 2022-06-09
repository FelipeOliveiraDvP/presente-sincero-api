<?php

namespace App\Http\Middleware;

use App\Traits\AbilitiesHelper;
use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    use AbilitiesHelper;

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

        $abilities = $this->sellerAbilities();

        foreach ($abilities as $ability) {
            if (!$user->tokenCan($ability)) {
                return response()->json(['message' => 'Você não tem permissão para essa ação'], 403);
            }
        }

        return $next($request);
    }
}
