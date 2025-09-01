<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    /**
     * Login and return a personal access token.
     */
     public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'    => 'required|string|max:255',
            'middle_name'   => 'nullable|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users',
            'phone_number'  => 'nullable|string|max:20',
            'gender'        => 'nullable|in:female,male,prefer_not_say',
            'birthday'      => 'nullable|date',
            'password'      => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'first_name'    => $request->first_name,
            'middle_name'   => $request->middle_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'phone_number'  => $request->phone_number,
            'gender'        => $request->gender,
            'birthday'      => $request->birthday,
            'password'      => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'User registered successfully',
            'token'   => $token,
            'user'    => $user
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        // delete old tokens before issuing a new one (optional)
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => $user
        ], 200);
    }
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            // currentAccessToken() may be typed as HasAbilities by some analyzers.
            // Use a phpdoc to tell the analyzer this is a PersonalAccessToken instance.
            /** @var PersonalAccessToken|null $token */
            $token = $user->currentAccessToken();

            if ($token instanceof PersonalAccessToken) {
                // analyzer and runtime both happy
                $token->delete();
            } else {
                // Fallback: if analyzer still can't see the id, delete by token id if present
                // (this branch is defensive — usually token will be PersonalAccessToken)
                if (is_object($token) && property_exists($token, 'id')) {
                    $user->tokens()->where('id', $token->id)->delete();
                }
            }
        }

        return response()->json(['message' => 'Logged out'], 200);
    }
     public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'user'   => $request->user()
        ], 200);
    }
   public function user(Request $request)
    {
        $user = $request->user(); // ✅ safer, uses sanctum auth

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        return response()->json([
            'id'           => $user->id,
            'first_name'   => $user->first_name,
            'middle_name'  => $user->middle_name,
            'last_name'    => $user->last_name,
            // For backward compatibility with older clients
            'name'         => trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name),
            'email'        => $user->email,
            'phone_number' => $user->phone_number,
            'gender'       => $user->gender,
            'birthday'     => $user->birthday,
            'role'         => $user->role,
        ]);
    }
    
    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'first_name'            => 'sometimes|required|string|max:255',
            'middle_name'           => 'nullable|string|max:255',
            'last_name'             => 'sometimes|required|string|max:255',
            'email'                 => 'sometimes|required|email|unique:users,email,' . $user->id,
            'phone_number'          => 'nullable|string|max:20',
            'gender'                => 'nullable|in:female,male,prefer_not_say',
            'birthday'              => 'nullable|date',
            'password'              => 'nullable|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }
        
        // Update only the fields that were provided
        if ($request->has('first_name')) {
            $user->first_name = $request->first_name;
        }
        
        if ($request->has('middle_name')) {
            $user->middle_name = $request->middle_name;
        }
        
        if ($request->has('last_name')) {
            $user->last_name = $request->last_name;
        }
        
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        
        if ($request->has('phone_number')) {
            $user->phone_number = $request->phone_number;
        }
        
        if ($request->has('gender')) {
            $user->gender = $request->gender;
        }
        
        if ($request->has('birthday')) {
            $user->birthday = $request->birthday;
        }
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        
        return response()->json([
            'status'  => true,
            'message' => 'Profile updated successfully',
            'user'    => [
                'id'           => $user->id,
                'first_name'   => $user->first_name,
                'middle_name'  => $user->middle_name,
                'last_name'    => $user->last_name,
                'name'         => trim($user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name),
                'email'        => $user->email,
                'phone_number' => $user->phone_number,
                'gender'       => $user->gender,
                'birthday'     => $user->birthday,
                'role'         => $user->role,
            ]
        ], 200);
    }
}
