<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Interfaces\MustVerifyMobile;
use App\Models\LoginAttempt;
use App\Models\SMSVerify;
use App\Models\User;
use Carbon\Carbon;
use App\Traits\MustVerifyMobile as TraitsMustVerifyMobile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller implements MustVerifyMobile
{

    use TraitsMustVerifyMobile;

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Track login attempt
            $this->trackLoginAttempt($user);

            // sending sms to the user
            $this->sendMobileVerificationNotification($user);
            return response()->json([
                'phone' => $user->phone,
                'message' => 'Verification code sent to your number. Please verify to proceed.'
            ], 200);
        }
        return response()->json(['message' => 'Invalid credentials'], 401);
    }


    public function verifyCode(Request $request)
    {
        $user = User::where('phone', $request->phone)->first();
        if (!$user) {
            return response()->json(['message' => 'Invalid credentials']);
        }
        $userPhone = $request->phone;
        $verificationCode = $request->verification_code;
        $storedCode = SMSVerify::where('phone', $userPhone)->first();
        $this->trackLoginAttempt($user);

        if (!$storedCode) {
            return response()->json(['message' => 'SMS verification expired'], 401);
        }

        // Check if the verification code has expired
        if ($this->isVerificationCodeExpired($storedCode)) {
            $storedCode->delete();
            return response()->json(['message' => 'SMS verification code expired'], 401);
        }

        if ($verificationCode === $storedCode->verification_code) {
            $user = User::where('phone', $userPhone)->first();
            SMSVerify::where('user_id', $user->id)->delete();
            Auth::login($user);
            $token = $user->createToken($user->email)->plainTextToken;
            return response()->json([
                'token' => $token,
                'message' => 'Login successful'
            ], 200);
        } else {
            $attemptsRemaining = $storedCode->attempts;

            if ($attemptsRemaining <= 0) {
                $storedCode->delete();
                return response()->json(['message' => 'SMS verification code expired'], 401);
            }
            $storedCode->decrement('attempts', 1);
        }

        return response()->json(['message' => 'Invalid verification code'], 401);
    }

    protected function trackLoginAttempt($user)
    {
        $attempt = new LoginAttempt();
        $attempt->user_id = $user->id;
        $attempt->attempted_at = now();
        $attempt->save();
    }

    protected function isVerificationCodeExpired($storedCode)
    {
        $expirationTime = Carbon::parse($storedCode->created_at)->addMinutes(3);
        return Carbon::now()->greaterThan($expirationTime);
    }
}
