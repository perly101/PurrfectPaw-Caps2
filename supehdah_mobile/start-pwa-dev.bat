@echo off
echo ======================================================
echo    PurrfectPaw PWA Development Environment
echo ======================================================
echo.
echo This script will:
echo  1. Start Laravel Backend (if not already running)
echo  2. Apply React Navigation patch to fix web issues
echo  3. Start Expo web development server
echo.
echo NOTE: Use the ?bypass=true parameter if you see API errors
echo       http://localhost:19006/?bypass=true
echo.
echo ======================================================
echo.

REM Check if Node.js is installed
where node >nul 2>nul
if %ERRORLEVEL% neq 0 (
    echo Error: Node.js is not installed or not in PATH.
    echo Please install Node.js from https://nodejs.org/
    pause
    exit /b 1
)

echo [1/3] Starting Laravel Backend...
start cmd /k "cd ..\supehdah && php artisan serve --host=0.0.0.0 --port=8000"
echo Waiting for Laravel to start...
timeout /t 5

echo [2/3] Applying React Navigation patch...
call .\apply-navigation-patch.bat
if %ERRORLEVEL% neq 0 (
    echo Warning: Navigation patch may have failed.
    pause
)

echo [3/3] Starting Expo web server...
echo.
echo ******************************************************
echo * PWA will be available at: http://localhost:19006   *
echo *                                                    *
echo * NOTE: If you see API connection errors:            *
echo * Add ?bypass=true to URL to bypass API checks       *
echo * Example: http://localhost:19006/?bypass=true       *
echo ******************************************************
echo.

npx expo start --web

pause