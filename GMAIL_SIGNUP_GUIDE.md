# Gmail Sign-Up Implementation Guide for PurrfectPaw App

This guide provides step-by-step instructions for implementing Gmail sign-up in your React Native mobile application.

## Prerequisites

1. Google Cloud Platform account
2. Access to Google Cloud Console
3. Expo development environment

## Step 1: Set Up Google Cloud OAuth 2.0 Credentials

1. Go to the [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select your existing project
3. Navigate to "APIs & Services" > "Credentials"
4. Click "Create Credentials" and select "OAuth client ID"
5. Configure the OAuth consent screen:
   - Add your app name
   - Add your support email
   - Add authorized domains
6. Create OAuth client ID:
   - Application type: "Web application" for Expo web
   - Add authorized JavaScript origins: `https://auth.expo.io`
   - Add authorized redirect URIs: `https://auth.expo.io/@your-expo-username/your-app-slug`
7. Create another OAuth client ID for Android:
   - Application type: "Android"
   - Package name: Your app's package name (e.g., `com.purrfectpaw.app`)
   - Generate a SHA-1 certificate fingerprint using the command:
     ```
     keytool -list -v -keystore ~/.android/debug.keystore -alias androiddebugkey -storepass android -keypass android
     ```
8. Create another OAuth client ID for iOS:
   - Application type: "iOS"
   - Bundle ID: Your app's bundle ID (e.g., `com.purrfectpaw.app`)

## Step 2: Install Required Libraries

```bash
# Install required packages
npm install expo-auth-session expo-web-browser @react-native-async-storage/async-storage
```

## Step 3: Create GoogleAuthService

Create a file called `googleAuthService.js` in your `src` directory with the following content:

```javascript
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Platform } from 'react-native';
import { API } from './api';
import * as Google from 'expo-auth-session/providers/google';
import * as WebBrowser from 'expo-web-browser';

// Register WebBrowser for handling redirects
WebBrowser.maybeCompleteAuthSession();

// Configure Google OAuth client IDs for each platform
const GOOGLE_CLIENT_ID = Platform.select({
  ios: 'YOUR_IOS_CLIENT_ID.apps.googleusercontent.com',
  android: 'YOUR_ANDROID_CLIENT_ID.apps.googleusercontent.com',
  web: 'YOUR_WEB_CLIENT_ID.apps.googleusercontent.com',
}) || '';

// Define the Google auth scopes
const GOOGLE_SCOPES = ['profile', 'email'];

export class GoogleAuthService {
  static #request;
  static #response;
  static #promptAsync;

  // Initialize Google Auth
  static initGoogleAuth() {
    const [request, response, promptAsync] = Google.useAuthRequest({
      expoClientId: GOOGLE_CLIENT_ID,
      iosClientId: Platform.OS === 'ios' ? GOOGLE_CLIENT_ID : undefined,
      androidClientId: Platform.OS === 'android' ? GOOGLE_CLIENT_ID : undefined,
      webClientId: Platform.OS === 'web' ? GOOGLE_CLIENT_ID : undefined,
      scopes: GOOGLE_SCOPES,
    });

    this.#request = request;
    this.#response = response;
    this.#promptAsync = promptAsync;

    return { request, response, promptAsync };
  }

  // Regular email/password login
  static async loginWithEmail(email, password) {
    try {
      const response = await API.post('login', {
        email,
        password,
      });

      if (response.data && response.data.token) {
        await this.storeToken(response.data.token);
        return {
          success: true,
          user: response.data.user,
          isEmailVerified: response.data.user?.email_verified_at !== null,
        };
      } else {
        throw new Error(response.data?.message || 'Login failed');
      }
    } catch (error) {
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Login failed',
      };
    }
  }

  // Store authentication token
  static async storeToken(token) {
    await AsyncStorage.setItem('token', token);
    API.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    return token;
  }

  // Handle Google authentication for sign up
  static async handleGoogleSignUp(response) {
    // Extract user information from Google response
    const userInfo = await this.getUserInfoFromGoogle(response);
    
    // Return user info for registration
    return {
      success: true,
      userData: {
        first_name: userInfo.given_name || '',
        last_name: userInfo.family_name || '',
        email: userInfo.email || '',
        google_id: userInfo.id,
        avatar: userInfo.picture
      }
    };
  }

  // General method to handle Google authentication response
  static async handleGoogleResponse(response) {
    if (response?.type === 'success') {
      try {
        // Get the access token from the response
        const { access_token } = response.params;
        
        // Get user info from Google
        const userInfo = await this.getUserInfoFromGoogle(response);

        // Try to authenticate with backend using Google credentials
        const authResponse = await API.post('login/google', {
          google_id: userInfo.id,
          email: userInfo.email,
          first_name: userInfo.given_name,
          last_name: userInfo.family_name,
          avatar: userInfo.picture,
        });
        
        if (authResponse.data && authResponse.data.token) {
          await this.storeToken(authResponse.data.token);
          
          return {
            success: true,
            user: authResponse.data.user,
            isEmailVerified: authResponse.data.user?.email_verified_at !== null,
          };
        } else {
          throw new Error(authResponse.data?.message || 'Google auth failed');
        }
      } catch (error) {
        console.error('Error in handleGoogleResponse:', error);
        return {
          success: false,
          error: error.response?.data?.message || error.message || 'Google authentication failed',
        };
      }
    } else {
      return {
        success: false,
        error: 'Google authentication was cancelled or failed',
      };
    }
  }

  // Get user information from Google
  static async getUserInfoFromGoogle(response) {
    try {
      const { access_token } = response.params;
      const userInfoResponse = await fetch('https://www.googleapis.com/userinfo/v2/me', {
        headers: { Authorization: `Bearer ${access_token}` },
      });
      
      if (!userInfoResponse.ok) {
        throw new Error('Failed to fetch user info from Google');
      }
      
      const userInfo = await userInfoResponse.json();
      return userInfo;
    } catch (error) {
      console.error('Error fetching user info from Google:', error);
      throw error;
    }
  }

  // Register with Google data
  static async registerWithGoogle(userData) {
    try {
      const response = await API.post('register-google', userData);
      
      if (response.data && response.data.token) {
        await this.storeToken(response.data.token);
        return {
          success: true,
          user: response.data.user,
          isEmailVerified: response.data.user?.email_verified_at !== null,
          otp_sent: response.data.otp_sent || false,
        };
      } else {
        throw new Error(response.data?.message || 'Registration failed');
      }
    } catch (error) {
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Registration failed',
      };
    }
  }
}
```

## Step 4: Update RegisterScreen.tsx

Update your RegisterScreen.tsx to support Gmail sign-up:

```jsx
// Add imports for Google Sign Up
import * as WebBrowser from 'expo-web-browser';
import { AntDesign } from '@expo/vector-icons';
import { useEffect } from 'react';
import { GoogleAuthService } from '../src/googleAuthService';

// Inside RegisterScreen component, add:
const [googleSignupLoading, setGoogleSignupLoading] = useState(false);

// Initialize Google Auth hook
useEffect(() => {
  // Initialize Google Auth on component mount
  const { request, response, promptAsync } = GoogleAuthService.initGoogleAuth();
  
  // Handle Google auth response
  const handleGoogleResponse = async () => {
    if (response?.type === 'success') {
      setGoogleSignupLoading(true);
      
      try {
        // Process Google signup
        const result = await GoogleAuthService.handleGoogleSignUp(response);
        
        if (result.success) {
          // Pre-fill form with Google data
          setFirstName(result.userData.first_name);
          setLastName(result.userData.last_name);
          setEmail(result.userData.email);
          
          // Show success notification
          Alert.alert(
            "Google Account Connected",
            "Your Google information has been added. Please complete the registration form."
          );
        } else {
          Alert.alert("Error", result.error || "Failed to connect Google account");
        }
      } catch (error) {
        console.error("Google signup error:", error);
        Alert.alert("Error", "Failed to process Google sign up");
      } finally {
        setGoogleSignupLoading(false);
      }
    }
  };
  
  if (response) {
    handleGoogleResponse();
  }
}, [response]);

// Add a Google Sign Up button in the UI
const handleGoogleSignUp = async () => {
  try {
    const { promptAsync } = GoogleAuthService.initGoogleAuth();
    await promptAsync();
  } catch (error) {
    console.error("Error initiating Google signup:", error);
    Alert.alert("Error", "Could not connect to Google. Please try again.");
  }
};

// Add this in your JSX below the regular registration form
<View style={styles.separator}>
  <View style={styles.line} />
  <Text style={styles.separatorText}>OR</Text>
  <View style={styles.line} />
</View>

<TouchableOpacity 
  style={styles.googleButton} 
  onPress={handleGoogleSignUp}
  disabled={loading || googleSignupLoading}
>
  {googleSignupLoading ? (
    <ActivityIndicator color="#fff" />
  ) : (
    <>
      <AntDesign name="google" size={24} color="white" style={styles.googleIcon} />
      <Text style={styles.googleButtonText}>Continue with Google</Text>
    </>
  )}
</TouchableOpacity>
```

## Step 5: Set Up Backend API Endpoint

Create an API endpoint on your Laravel backend to handle Google authentication registration:

```php
// routes/api.php
Route::post('register-google', [AuthController::class, 'registerWithGoogle']);

// app/Http/Controllers/AuthController.php
public function registerWithGoogle(Request $request)
{
    // Validate input
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|unique:users,email',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'google_id' => 'required|string',
        'avatar' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    DB::beginTransaction();
    
    try {
        // Generate a random password for the user
        $password = Str::random(16);
        
        // Create the user
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'google_id' => $request->google_id,
            'avatar' => $request->avatar,
            'email_verified_at' => now(), // Google accounts are already verified
        ]);
        
        // Create additional user details if needed
        UserDetail::create([
            'user_id' => $user->id,
            // Add any additional details here
        ]);
        
        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;
        
        DB::commit();
        
        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => $user,
        ]);
    } catch (\Exception $e) {
        DB::rollback();
        
        return response()->json([
            'status' => false,
            'message' => 'Registration failed: ' . $e->getMessage()
        ], 500);
    }
}
```

## Step 6: Testing

1. Run your application using Expo
2. Navigate to the Register screen
3. Click on the "Continue with Google" button
4. Follow the Google authentication flow
5. Verify that the form is pre-filled with Google data
6. Complete any additional required fields
7. Submit the form
8. Verify that the user is registered successfully

## Troubleshooting

1. **OAuth Redirect Issues**:
   - Ensure your redirect URIs are correctly configured in Google Cloud Console
   - For Expo, the redirect URI should be `https://auth.expo.io/@your-expo-username/your-app-slug`

2. **Invalid Client ID**:
   - Verify that you're using the correct client ID for each platform
   - Double-check that your client IDs are correctly configured in GoogleAuthService.js

3. **Backend Integration Issues**:
   - Check your Laravel routes and controller methods
   - Verify that your API is correctly handling the Google user data

4. **Debugging Authentication Flow**:
   - Add console logs at each step of the process
   - Check network requests to identify any issues with the API calls

## Security Considerations

1. **Token Storage**:
   - Always store authentication tokens securely using AsyncStorage
   - Consider encrypting sensitive data

2. **Backend Validation**:
   - Always validate Google tokens on your backend
   - Implement rate limiting to prevent abuse

3. **User Privacy**:
   - Only request the necessary scopes from Google
   - Be transparent with users about what data you're accessing

## Additional Resources

1. [Expo Auth Session Documentation](https://docs.expo.dev/versions/latest/sdk/auth-session/)
2. [Google OAuth 2.0 Documentation](https://developers.google.com/identity/protocols/oauth2)
3. [Laravel Sanctum Documentation](https://laravel.com/docs/8.x/sanctum)