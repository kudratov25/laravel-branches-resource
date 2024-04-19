<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserIsActiveMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) {
            return $next($request);
        }
        if ($user->is_active === 1) {
            $user->update([
                'last_active_at' => now(),
            ]);
        } else {
            return response()->json(['message' => 'User blocked due to inactivity'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
