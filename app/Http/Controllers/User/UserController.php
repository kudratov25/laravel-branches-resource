<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return UserResource::collection($users);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users|max:255',
                'phone' => ['required', 'string', 'max:255', 'unique:users', 'regex:/^[0-9\-\+\(\)\/\s]*$/'],
                'password' => 'required|string|min:8',
            ]);
            $validatedData['password'] = Hash::make($request->password);
            $user = User::create($validatedData);

            return response()->json($user, 200);
        } catch (ValidationException $e) {
            // Log the error
            Log::error('Error occurred while storing user: ' . $e->getMessage());

            // Return error response
            return response()->json(['errors' => $e->errors()], 500);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'email|max:255|unique:users,email,' . $id,
                'phone' => ['string','max:255',Rule::unique('users')->ignore($id),'regex:/^[0-9\-\+\(\)\/\s]*$/'],
                'password' => 'string|min:8',
            ]);
            $validatedData['password'] = Hash::make($request->password);
            $user = User::findOrFail($id);
            $user->update($validatedData);
            return response()->json($user, 200);
        } catch (ValidationException $e) {
            // Log the error
            Log::error('Error occurred while updating user: ' . $e->getMessage());

            // Return error response
            return response()->json(['errors' => $e->errors()], 500);
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
