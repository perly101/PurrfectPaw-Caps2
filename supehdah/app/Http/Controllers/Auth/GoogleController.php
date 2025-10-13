<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Socialite\Socialite;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')->redirect();
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Google authentication failed: ' . $e->getMessage());
        }
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user exists
            $existingUser = User::where('google_id', $googleUser->id)
                                ->orWhere('email', $googleUser->email)
                                ->first();
            
            if ($existingUser) {
                // Update Google info if needed
                if (empty($existingUser->google_id)) {
                    $existingUser->update([
                        'google_id' => $googleUser->id,
                        'google_token' => $googleUser->token,
                        'google_refresh_token' => $googleUser->refreshToken,
                        'avatar' => $googleUser->avatar,
                    ]);
                }
                
                Auth::login($existingUser);
                return redirect()->intended('/');
            }
            
            // Create new user
            $nameParts = $this->splitName($googleUser->name);
            
            $newUser = User::create([
                'first_name' => $nameParts['first_name'],
                'last_name' => $nameParts['last_name'],
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
                'avatar' => $googleUser->avatar,
                'password' => null,
                'role' => 'user', // Default role
                'email_verified_at' => now(),
            ]);
            
            event(new Registered($newUser));
            
            Auth::login($newUser);
            return redirect()->intended('/');
            
        } catch (Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Google authentication failed: ' . $e->getMessage());
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
        $nameParts = explode(' ', $fullName, 2);
        
        return [
            'first_name' => $nameParts[0],
            'last_name' => isset($nameParts[1]) ? $nameParts[1] : '',
        ];
    }
}