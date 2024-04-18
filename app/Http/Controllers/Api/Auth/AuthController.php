<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // logout user
    public function logout(Request $request)
    {
        $user = auth()->user();
        if ($user->currentAccessToken()->delete()) {
            return response()->json([$user->name => 'logged out']);
        }
    }
}
