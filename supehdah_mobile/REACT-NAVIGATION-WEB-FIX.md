# React Navigation Web Compatibility Fix

This document outlines the steps taken to fix the "require is not defined" error in the PurrfectPaw PWA.

## Problem

The error `Uncaught ReferenceError: require is not defined` was occurring in the web version of the app, specifically with the `@react-navigation/elements` package. This happens because React Navigation uses Node.js-style `require()` calls in some of its modules, which are not natively supported in web browsers.

## Solution

We applied the following fixes:

1. **Created a patched version of the problematic file**
   - Created a custom implementation of `useFrameSize.js` that doesn't use `require()`
   - Applied the patch to the node_modules file

2. **Added polyfills for Node.js core modules**
   - Updated webpack.config.js to include polyfills for Node.js core modules
   - Installed necessary polyfill packages

3. **Added runtime polyfill for `require`**
   - Created a polyfill that provides mock implementations for specific modules
   - Imported the polyfill at the entry point of the app

4. **Updated babel configuration**
   - Created a babel plugin to transform `require()` calls for specific modules
   - Configured babel to apply the plugin only in web environment

## Files Modified

1. `webpack.config.js` - Added Node.js polyfills
2. `src/polyfills.js` - Created runtime polyfills
3. `index.ts` - Imported polyfills at app startup
4. `babel.config.js` - Added configuration for web-specific transformations
5. `navigation-patch/useFrameSize.js` - Created patched implementation
6. `apply-navigation-patch.bat` - Script to apply the patch

## How to Apply the Fix

1. Install the necessary polyfill packages:
   ```
   npm install --save-dev crypto-browserify stream-browserify assert stream-http https-browserify os-browserify url path-browserify browserify-zlib util buffer process
   ```

2. Run the patch script:
   ```
   apply-navigation-patch.bat
   ```

3. Start the app:
   ```
   npx expo start --web
   ```

## Notes

- This is a temporary fix until React Navigation provides better web support
- If you update your dependencies, you may need to reapply the patch
- The polyfills increase the bundle size slightly but are necessary for web compatibility