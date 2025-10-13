// This file contains utility functions to test API connectivity in different ways
// It's used by App.tsx to diagnose connection issues

import axios from 'axios';
import { Platform } from 'react-native';

// Try multiple base URLs to find one that works
export const tryMultipleBaseUrls = async (endpoint: string) => {
  const possibleBaseUrls = [
    'http://localhost:8000/api',
    'http://127.0.0.1:8000/api',
    'http://192.168.1.10:8000/api',
    'http://10.0.2.2:8000/api', // Android emulator localhost
    window.location?.origin + '/api', // Same domain
  ];

  console.log('🔄 Trying multiple base URLs for endpoint:', endpoint);
  
  // Try each base URL
  for (const baseUrl of possibleBaseUrls) {
    try {
      console.log(`🔄 Trying ${baseUrl}${endpoint}`);
      const response = await axios.get(`${baseUrl}${endpoint}`, {
        timeout: 3000, // Short timeout for quick tests
        headers: {
          'Accept': 'application/json'
        }
      });
      
      if (response.status === 200) {
        console.log(`✅ Success with ${baseUrl}`);
        return {
          success: true,
          baseUrl,
          data: response.data
        };
      }
    } catch (error) {
      console.log(`❌ Failed with ${baseUrl}:`, (error as Error).message);
      // Continue to the next URL
    }
  }
  
  // All attempts failed
  return {
    success: false,
    baseUrl: null,
    data: null
  };
};

// Test if a server is running but CORS is blocking the request
export const testCorsIssue = async (baseUrl: string) => {
  if (Platform.OS !== 'web') {
    return { isCorsIssue: false };
  }
  
  try {
    // Try with fetch which shows different error for CORS issues
    const response = await fetch(`${baseUrl}/health-check`, {
      method: 'GET',
      mode: 'no-cors', // This will succeed even with CORS issues but return opaque response
      signal: AbortSignal.timeout(3000)
    });
    
    // If we get here with no-cors mode, server is reachable but might have CORS issues
    const corsTestWithNormalMode = await fetch(`${baseUrl}/health-check`, {
      method: 'GET',
      // No 'no-cors' here to see if it fails
      signal: AbortSignal.timeout(3000)
    }).catch(e => {
      // If this errors but the no-cors succeeded, it's likely a CORS issue
      return { corsError: true, error: e };
    });
    
    return {
      isCorsIssue: typeof (corsTestWithNormalMode as any).corsError === 'boolean' && (corsTestWithNormalMode as any).corsError === true,
      serverReachable: true
    };
  } catch (error) {
    // Server completely unreachable
    return {
      isCorsIssue: false,
      serverReachable: false
    };
  }
};

// Get readable error message for API connection issues
export const getApiErrorMessage = (error: any) => {
  if (!error) {
    return 'Unknown error occurred';
  }
  
  if (error.message && error.message.includes('timeout')) {
    return 'Connection timed out. The server may be down or unreachable.';
  }
  
  if (error.message && error.message.includes('Network Error')) {
    return 'Network error. Please check your internet connection and ensure the server is running.';
  }
  
  if (error.response) {
    // Server responded with error
    if (error.response.status === 404) {
      return 'API endpoint not found (404). Check if the API URL is correct.';
    }
    if (error.response.status === 403) {
      return 'Access forbidden (403). You may not have permission to access the API.';
    }
    if (error.response.status === 401) {
      return 'Authentication required (401). You need to login first.';
    }
    if (error.response.status >= 500) {
      return `Server error (${error.response.status}). The backend server has encountered an error.`;
    }
    return `Server responded with error code ${error.response.status}`;
  }
  
  if (error.request) {
    // Request was made but no response
    return 'No response from server. The server might be down or unreachable.';
  }
  
  // Default error message
  return error.message || 'An unknown error occurred';
};