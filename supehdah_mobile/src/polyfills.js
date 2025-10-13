// React Navigation Elements Polyfill
// This patch fixes the "require is not defined" error in web environment

// Polyfill for useFrameSize.js in @react-navigation/elements
if (typeof window !== 'undefined') {
  // Only apply in web environment
  try {
    const originalRequire = window.require;
    
    // Create a mock require function for specific modules
    window.require = function(modulePath) {
      if (modulePath === 'react-native-safe-area-context') {
        // Return a mock implementation of the safe area context
        return {
          useSafeAreaFrame: function() {
            return {
              width: window.innerWidth,
              height: window.innerHeight,
              x: 0,
              y: 0
            };
          },
          useSafeAreaInsets: function() {
            return { top: 0, right: 0, bottom: 0, left: 0 };
          }
        };
      } else if (originalRequire) {
        return originalRequire(modulePath);
      }
      
      // For any other module, return an empty object
      return {};
    };
    
    console.log('✅ Applied React Navigation polyfill for web');
  } catch (error) {
    console.error('❌ Failed to apply React Navigation polyfill:', error);
  }
}