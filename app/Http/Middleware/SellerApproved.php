<?php

namespace App\Http\Middleware;

use App\Traits\AuthHelper;
use Closure;
use Illuminate\Http\Request;

class SellerApproved
{
    use AuthHelper;

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

        if ($this->isSeller($user) && !$user->seller_approved) {
            return response()->json(['message' => 'Sua conta ainda não foi aprovada!'], 403);
        }

        return $next($request);
    }
}
