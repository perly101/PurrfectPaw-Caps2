// GoogleAuthService.js for Gmail Sign-Up Implementation
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Platform } from 'react-native';
import { API } from './api';
import * as Google from 'expo-auth-session/providers/google';
import * as WebBrowser from 'expo-web-browser';

// Register WebBrowser for handling redirects
WebBrowser.maybeCompleteAuthSession();

// Configure your Google OAuth client IDs - replace with your actual client IDs
const GOOGLE_CLIENT_ID = Platform.select({
  ios: 'YOUR_IOS_CLIENT_ID.apps.googleusercontent.com',
  android: 'YOUR_ANDROID_CLIENT_ID.apps.googleusercontent.com',
  web: 'YOUR_WEB_CLIENT_ID.apps.googleusercontent.com',
}) || '';

export class GoogleAuthService {
  // Initialize Google Auth - call this in your component with React hooks
  static initGoogleAuth() {
    const [request, response, promptAsync] = Google.useAuthRequest({
      expoClientId: GOOGLE_CLIENT_ID,
      iosClientId: Platform.OS === 'ios' ? GOOGLE_CLIENT_ID : undefined,
      androidClientId: Platform.OS === 'android' ? GOOGLE_CLIENT_ID : undefined,
      webClientId: Platform.OS === 'web' ? GOOGLE_CLIENT_ID : undefined,
      scopes: ['profile', 'email']
    });

    return { request, response, promptAsync };
  }

  // Handle Google Sign-Up
  static async handleGoogleSignUp(response) {
    if (response?.type !== 'success') {
      return {
        success: false,
        error: 'Google authentication was cancelled or failed'
      };
    }

    try {
      // Get user info from Google
      const userInfo = await this.getUserInfoFromGoogle(response);
      
      // Send the Google user info to your backend to create a new account
      const registerResponse = await API.post('register-google', {
        google_id: userInfo.id,
        email: userInfo.email,
        first_name: userInfo.given_name,
        last_name: userInfo.family_name,
        avatar: userInfo.picture
      });
      
      if (registerResponse.data && registerResponse.data.token) {
        // Store authentication token
        await this.storeToken(registerResponse.data.token);
        
        return {
          success: true,
          user: registerResponse.data.user,
          isEmailVerified: registerResponse.data.user?.email_verified_at !== null,
          otp_sent: registerResponse.data.otp_sent || false
        };
      } else {
        throw new Error(registerResponse.data?.message || 'Registration failed');
      }
    } catch (error) {
      console.error('Error in Google sign-up:', error);
      
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Google sign-up failed'
      };
    }
  }
  
  // Pre-fill registration form with Google data
  static async getGoogleSignUpInfo(response) {
    if (response?.type !== 'success') {
      return {
        success: false,
        error: 'Google authentication was cancelled or failed'
      };
    }
    
    try {
      // Get user info from Google
      const userInfo = await this.getUserInfoFromGoogle(response);
      
      // Return user info for registration form pre-fill
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
    } catch (error) {
      console.error('Error getting Google sign-up info:', error);
      
      return {
        success: false,
        error: error.message || 'Failed to get Google user information'
      };
    }
  }

  // Get user information from Google
  static async getUserInfoFromGoogle(response) {
    const { access_token } = response.params;
    
    const userInfoResponse = await fetch('https://www.googleapis.com/userinfo/v2/me', {
      headers: { Authorization: `Bearer ${access_token}` }
    });
    
    if (!userInfoResponse.ok) {
      throw new Error('Failed to fetch user info from Google');
    }
    
    return await userInfoResponse.json();
  }

  // Store authentication token
  static async storeToken(token) {
    await AsyncStorage.setItem('token', token);
    API.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    return token;
  }

  // Regular email/password login (for comparison)
  static async loginWithEmail(email, password) {
    try {
      const response = await API.post('login', { email, password });

      if (response.data && response.data.token) {
        await this.storeToken(response.data.token);
        return {
          success: true,
          user: response.data.user,
          isEmailVerified: response.data.user?.email_verified_at !== null
        };
      } else {
        throw new Error(response.data?.message || 'Login failed');
      }
    } catch (error) {
      return {
        success: false,
        error: error.response?.data?.message || error.message || 'Login failed'
      };
    }
  }
}