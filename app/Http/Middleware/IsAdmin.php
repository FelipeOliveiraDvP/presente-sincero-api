<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IsAdmin
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

        $abilities = [
            'manage:contests',
            'view:contests',
            'manage:bank_accounts',
            'view:bank_accounts',
            'manage:upload',
        ];

        foreach ($abilities as $ability) {
            if (!$user->tokenCan($ability)) {
                return response()->json(['message' => 'Você não tem permissão para essa ação'], 403);
            }
        }

        return $next($request);
    }
}
