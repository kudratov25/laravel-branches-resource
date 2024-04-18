<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Interfaces\MustVerifyMobile;
use App\Models\User;
use App\Traits\MustVerifyMobile as TraitsMustVerifyMobile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller implements MustVerifyMobile
{

    use TraitsMustVerifyMobile;
    // register user
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => ['required', 'unique:users', 'email:rfc,dns'],
                'password' => 'required|string|min:8',
                'c_password' => 'required|same:password',
                'phone' => ['required', 'string', 'unique:users']
            ]);

            $user = new User([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone
            ]);

            if ($user->save()) {

                $this->sendMobileVerificationNotification($user);
                return response()->json([
                    'message' => 'Verification code sent to your number. Please verify to proceed.',
                    'phone' => $user->phone
                ], 200);
            }
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
}
