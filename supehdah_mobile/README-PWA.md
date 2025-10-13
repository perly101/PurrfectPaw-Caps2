# PurrfectPaw PWA Development Guide

This document provides instructions for running and developing the PurrfectPaw Progressive Web App (PWA) version of the mobile application.

## Overview

The PurrfectPaw PWA is a web-based version of our React Native mobile app, allowing users to access the platform from any modern web browser. The PWA shares code with the mobile app while providing a seamless experience on desktop and mobile browsers.

## Prerequisites

- [Node.js](https://nodejs.org/) (v14 or higher)
- [npm](https://www.npmjs.com/) (v6 or higher)
- A modern web browser (Chrome, Firefox, Edge, or Safari)
- The Laravel backend server (running on localhost:8000 or a remote server)

## Getting Started

### 1. Start the Laravel Backend

The PWA requires the Laravel backend API to be running. Start it with:

```bash
cd ../supehdah  # Navigate to the Laravel project directory
php artisan serve --host=0.0.0.0 --port=8000
```

### 2. Start the PWA Development Server

We've created a simple static file server for PWA development to avoid CORS issues and simplify testing:

```bash
# On Windows
start-pwa-dev.bat

# On macOS/Linux
node server.js
```

This will start a development server at http://localhost:3000 that serves the PWA static files.

### 3. Access the PWA

Open your browser and navigate to:
- Main app: http://localhost:3000
- Debug page: http://localhost:3000/debug.html

## Troubleshooting

### API Connection Issues

If the app is stuck on the loading screen:

1. Open the debug page at http://localhost:3000/debug.html
2. Use the tools to test API connectivity
3. Check that the Laravel backend is running on the correct port
4. Verify CORS settings in the Laravel backend

### Bypassing API Check

During development, you can bypass the API check by adding `?bypass=true` to the URL:
```
http://localhost:3000/?bypass=true
```

This will skip the API connection check during app initialization.

### Common Issues

1. **App shows loading screen indefinitely**
   - Check browser console for errors
   - Verify the API URL configuration in `src/api.ts`
   - Use the debug page to test API connectivity

2. **API calls failing**
   - Check that the Laravel server is running
   - Verify CORS headers are properly set in Laravel
   - Check network tab in browser dev tools for specific errors

3. **Service worker errors**
   - Clear browser cache and site data
   - Unregister existing service workers through browser dev tools

## PWA Architecture

The PWA implementation uses:

1. **React Native for Web** - To share code with the mobile app
2. **Service Worker** - For offline support and caching
3. **Web Manifest** - For installation as a PWA on supported devices
4. **Express Server** - Simple static file server for development

## API Configuration

The API configuration automatically detects the environment and sets the appropriate base URL:

- Development: `http://localhost:8000/api`
- Production: Uses the same host as the PWA with `/api` path
- Mobile: Uses the configured IP address

## Development Tips

1. Use the debug page to diagnose API connection issues
2. Check browser console logs for detailed error messages
3. Use `?bypass=true` to skip API check during development
4. Clear cache and reload when testing service worker changes

## Production Deployment

For production deployment:

1. Build the React Native Web app
2. Copy the web build files to the Laravel public directory
3. Configure the web server to serve the PWA files
4. Ensure proper CORS settings are in place

---

For more information, contact the development team.