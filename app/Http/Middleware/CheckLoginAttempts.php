<?php

namespace App\Http\Middleware;

use App\Models\LoginAttempt;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class CheckLoginAttempts
{
    public function handle($request, Closure $next)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['email' => 'The provided credentials are incorrect.']);
        }
        $attemptLimit = config('auth.login_attempt_limit');
        $cooldownPeriod = config('auth.login_cooldown_period');

        $recentAttemptsCount = LoginAttempt::where('user_id', $user->id)
            ->where('attempted_at', '>', now()->subMinutes(config('auth.login_attempt_interval')))
            ->count();

        if ($recentAttemptsCount >= $attemptLimit) {
            $lastAttempt = LoginAttempt::where('user_id', $user->id)
                ->orderBy('attempted_at', 'desc')
                ->first();

            $lastAttemptTimestamp = Carbon::parse($lastAttempt->attempted_at);

            if ($lastAttemptTimestamp->addMinutes($cooldownPeriod)->isFuture()) {
                $waitTime = $lastAttemptTimestamp->addMinutes($cooldownPeriod)->diffForHumans(now(), true);
                return response()->json(['message' => 'Too many login attempts. Please try again in ' . $waitTime . '.'], 429);
            }
        }

        return $next($request);
    }
}
