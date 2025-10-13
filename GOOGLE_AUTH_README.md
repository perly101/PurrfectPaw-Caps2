# Google Authentication Implementation for PurrfectPaw Mobile App

This document outlines how Google Authentication has been implemented in the PurrfectPaw mobile app and provides guidance for testing.

## Implementation Overview

The Google Authentication flow in this app uses the Expo Auth Session API and supports both token-based and code-based authentication flows.

### Key Components:

1. **Frontend (Mobile App)**:
   - `GoogleAuthService.ts`: Handles the OAuth flow with Google
   - `LoginScreen.tsx`: Contains the Google Sign-In button and handles the auth response

2. **Backend (Laravel)**:
   - `GoogleMobileController.php`: Processes authentication callbacks and generates user tokens

## Authentication Flow

1. User taps the "Sign in with Google" button in the LoginScreen
2. The app opens a web browser session to the Google OAuth consent screen
3. User authenticates with Google and grants permissions
4. Google redirects back to the app with either an auth code or tokens (depending on flow)
5. The app sends the code/tokens to the backend
6. The backend verifies the credentials, creates/finds the user, and returns a Sanctum auth token

## Configuration Details

### Mobile App Configuration

- **app.json**: Contains the scheme, package name, and Google Services configuration
  - Scheme: `com.purrfectpaw.app`
  - Package name: `com.purrfectpaw.app`
  - SHA-1 fingerprint: `71:28:82:7D:2F:82:AE:87:B9:D2:3E:5C:43:39:3F:38:48:C6:B4:DB`

- **GoogleAuthService.ts**:
  - Google Client ID: `1057133190581-oats45nfs1uet4l8kjbffrouedck8aar.apps.googleusercontent.com`
  - Redirect URI: `com.purrfectpaw.app://oauth2callback`

### Backend Configuration

- **config/services.php**:
  - Contains Google Client ID, Client Secret, and Redirect URI from environment variables

## Testing Google Authentication

### Prerequisites

1. Ensure you have your environment variables set up in your `.env` file:
   ```
   GOOGLE_CLIENT_ID=1057133190581-oats45nfs1uet4l8kjbffrouedck8aar.apps.googleusercontent.com
   GOOGLE_CLIENT_SECRET=<your-client-secret>
   GOOGLE_REDIRECT_URI=com.purrfectpaw.app://oauth2callback
   ```

2. Make sure the SHA-1 fingerprint is added to your Google Cloud Console project.

### Testing Steps

1. **Start the Backend**:
   ```bash
   cd supehdah
   php artisan serve
   ```

2. **Start the Mobile App**:
   ```bash
   cd supehdah_mobile
   npm start
   ```

3. **Test the Authentication**:
   - Open the app in Expo Go
   - Navigate to the Login Screen
   - Tap "Sign in with Google"
   - Complete the authentication process
   - Check the logs for any errors

### Troubleshooting

1. **CORS Issues**:
   - If experiencing CORS issues, ensure your backend has the appropriate middleware enabled.

2. **Redirect URI Mismatch**:
   - Double check that the redirect URI in GoogleAuthService.ts matches the one registered in Google Cloud Console.

3. **SHA-1 Fingerprint**:
   - Ensure the SHA-1 fingerprint is correctly added to your Google Cloud Console project.
   - For Expo Go, you used the fingerprint: `71:28:82:7D:2F:82:AE:87:B9:D2:3E:5C:43:39:3F:38:48:C6:B4:DB`

4. **Debug Logs**:
   - Check both frontend console logs and backend logs (storage/logs/laravel.log) for debugging.

## Notes for Production

1. Replace the placeholder google-services.json with a real one from Firebase console
2. Consider implementing refresh tokens for longer sessions
3. Add additional error handling and user feedback
4. Implement proper token storage and refresh mechanism