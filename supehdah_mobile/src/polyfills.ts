// src/polyfills.ts - Import polyfills for web environment

import { Platform } from 'react-native';

// Only apply polyfills in web environment
if (Platform.OS === 'web') {
  // Set up global fetch timeout
  const originalFetch = window.fetch;
  // @ts-ignore
  window.fetch = async (input: RequestInfo | URL, init?: RequestInit) => {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout
    
    // Merge abort signal with any existing options
    const options = init || {};
    options.signal = controller.signal;
    
    try {
      const response = await originalFetch(input, options);
      clearTimeout(timeoutId);
      return response;
    } catch (error) {
      clearTimeout(timeoutId);
      throw error;
    }
  };
  
  console.log('✅ Web polyfills loaded');
}