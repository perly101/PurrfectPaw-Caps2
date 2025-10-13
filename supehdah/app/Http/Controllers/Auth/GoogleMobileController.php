<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Http;

class GoogleMobileController extends Controller
{
    /**
     * Handle Google OAuth callback for mobile app.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleCallback(Request $request)
    {
        try {
            \Log::debug('Google Mobile Auth Request received', [
                'params' => $request->all(),
                'headers' => $request->header(),
                'ip' => $request->ip(),
                'method' => $request->method()
            ]);
            
            // Support both token-based and code-based flows
            if ($request->has('code')) {
                // Handle authentication with authorization code
                \Log::debug('Using code-based flow');
                
                // This would require configuring socialite with proper redirect URI
                return $this->handleCodeBasedAuth($request);
            } else {
                // Token-based flow (current implementation)
                \Log::debug('Using token-based flow');
                
                // Validate the request for token flow
                $request->validate([
                    'access_token' => 'required|string',
                    'id_token' => 'required|string',
                ]);
                
                // Use Laravel's HTTP client to verify the token with Google's tokeninfo endpoint
                $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $request->id_token
                ]);
                
                if (!$response->successful()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid ID token'
                    ], 401);
                }
                
                $payload = $response->json();

            // Extract user data from payload
            $googleId = $payload['sub'];
            $email = $payload['email'] ?? null;
            $name = $payload['name'] ?? null;
            $picture = $payload['picture'] ?? null;
            
            // Verify that the token was intended for our app
            $clientId = config('services.google.client_id');
            if ($payload['aud'] !== $clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token was not issued for this application'
                ], 401);
            }

            // Check if user exists
            $user = User::where('google_id', $googleId)
                        ->orWhere('email', $email)
                        ->first();

            if ($user) {
                // Update Google info if needed
                if (empty($user->google_id)) {
                    $user->update([
                        'google_id' => $googleId,
                        'google_token' => $request->access_token,
                        'avatar' => $picture,
                    ]);
                }
            } else {
                // Create new user
                $nameParts = $this->splitName($name);
                
                $user = User::create([
                    'first_name' => $nameParts['first_name'],
                    'last_name' => $nameParts['last_name'],
                    'email' => $email,
                    'google_id' => $googleId,
                    'google_token' => $request->access_token,
                    'avatar' => $picture,
                    'password' => null,
                    'role' => 'user', // Default role
                    'email_verified_at' => now(),
                ]);
                
                event(new Registered($user));
            }

            // Generate token
            $token = $user->createToken('mobile-app')->plainTextToken;

            // Return response
            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user,
            ]);
        }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Google authentication failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle code-based authentication flow
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleCodeBasedAuth(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'code' => 'required|string',
            ]);
            
            // Get Google credentials
            $clientId = config('services.google.client_id');
            $clientSecret = config('services.google.client_secret');
            
            // Accept both native redirect and Expo proxy for development with Expo Go
            $redirectUri = $request->has('redirect_uri') 
                ? $request->redirect_uri 
                : 'https://auth.expo.io/@amelia00dawn/supehdah';
            
            // Exchange code for token
            $tokenResponse = Http::post('https://oauth2.googleapis.com/token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $request->code,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ]);
            
            if (!$tokenResponse->successful()) {
                \Log::error('Failed to exchange code for token', $tokenResponse->json());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to exchange authorization code'
                ], 400);
            }
            
            $tokens = $tokenResponse->json();
            
            // Get user info with access token
            $userInfoResponse = Http::withToken($tokens['access_token'])
                ->get('https://www.googleapis.com/oauth2/v3/userinfo');
                
            if (!$userInfoResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get user info'
                ], 400);
            }
            
            $userData = $userInfoResponse->json();
            
            // Extract user info
            $googleId = $userData['sub'];
            $email = $userData['email'] ?? null;
            $name = $userData['name'] ?? null;
            $picture = $userData['picture'] ?? null;
            
            // Find or create user (using the same logic as token-based flow)
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                $nameData = $this->splitName($name);
                
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => \Hash::make(\Str::random(16)),
                    'google_id' => $googleId,
                    'first_name' => $nameData['first_name'],
                    'last_name' => $nameData['last_name'],
                    'avatar' => $picture,
                    'email_verified_at' => now(),
                ]);
                
                $user->assignRole('user');
                
                event(new Registered($user));
            } else {
                // Update user's Google ID if not already set
                if (!$user->google_id) {
                    $user->google_id = $googleId;
                    $user->save();
                }
                
                // Mark email as verified if it wasn't already
                if (!$user->email_verified_at) {
                    $user->email_verified_at = now();
                    $user->save();
                }
            }
            
            // Generate token
            $token = $user->createToken('mobile-app')->plainTextToken;
            
            // Return response
            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Code-based auth error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Code-based authentication failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Split a full name into first name and last name parts.
     *
     * @param string $fullName
     * @return array
     */
    protected function splitName($fullName)
    {
        if (!$fullName) {
            return [
                'first_name' => '',
                'last_name' => '',
            ];
        }
        
        $nameParts = explode(' ', $fullName, 2);
        
        return [
            'first_name' => $nameParts[0],
            'last_name' => isset($nameParts[1]) ? $nameParts[1] : '',
        ];
    }
}