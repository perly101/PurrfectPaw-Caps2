// Type definitions for our auth service
declare module 'expo-auth-session' {
  // Augment existing types if needed
  export interface AuthSessionResult {
    type: 'success' | 'cancel' | 'dismiss' | 'locked';
    params?: Record<string, string>;
    error?: Error;
    url?: string;
  }
}