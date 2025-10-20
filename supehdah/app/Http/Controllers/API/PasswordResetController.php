<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users'],
        ], [
            'email.exists' => 'We could not find a user with that email address.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'status' => true,
                    'message' => 'Password reset link sent to your email'
                ], 200);
            } else {
                \Log::error('API Password reset failed', ['email' => $request->email, 'status' => $status]);
                return response()->json([
                    'status' => false,
                    'message' => __($status)
                ], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Exception during API password reset', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while sending the password reset link. Please try again later.'
            ], 500);
        }
    }
}