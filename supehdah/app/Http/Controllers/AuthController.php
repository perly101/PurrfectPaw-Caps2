<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone_number' => 'nullable|string|max:20',
            'gender' => 'nullable|in:female,male,prefer_not_say',
            'birthday' => 'nullable|date',
            'password' => 'required|min:6|confirmed'
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'birthday' => $request->birthday,
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token], 201);
    }
public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    $token = $user->createToken('mobile-token')->plainTextToken;

    return response()->json([
        'success' => true,
        'user' => $user,
        'token' => $token
    ]);
}


    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    }


    public function updateProfile(Request $request)
{
    $user = auth()->user();

    $validated = $request->validate([
        'first_name' => 'sometimes|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'last_name' => 'sometimes|string|max:255',
        'email' => 'sometimes|email|unique:users,email,' . $user->id,
        'phone_number' => 'nullable|string|max:20',
        'gender' => 'nullable|in:female,male,prefer_not_say',
        'birthday' => 'nullable|date',
        'password' => 'nullable|string|min:8|confirmed',
    ]);

    if (!empty($validated['password'])) {
        $validated['password'] = bcrypt($validated['password']);
    } else {
        unset($validated['password']);
    }

    $user->update($validated);

    return response()->json([
        'message' => 'Profile updated successfully',
        'user' => $user
    ]);
}
}
