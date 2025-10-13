// This file is imported by the babel.config.js to patch React Navigation issues in web

module.exports = function(api) {
  return {
    // Only apply in web environment
    visitor: {
      CallExpression(path) {
        // Look for require('react-native-safe-area-context') calls
        if (
          path.node.callee.name === 'require' &&
          path.node.arguments.length > 0 &&
          path.node.arguments[0].value === 'react-native-safe-area-context'
        ) {
          // Replace with a mock implementation for web
          const mockCode = `
          ({
            useSafeAreaFrame: function() {
              return {
                width: typeof window !== 'undefined' ? window.innerWidth : 0,
                height: typeof window !== 'undefined' ? window.innerHeight : 0,
                x: 0,
                y: 0
              };
            },
            useSafeAreaInsets: function() {
              return { top: 0, right: 0, bottom: 0, left: 0 };
            }
          })`;
          
          // Replace the require call with our mock object
          path.replaceWithSourceString(mockCode);
        }
      }
    }
  };
};