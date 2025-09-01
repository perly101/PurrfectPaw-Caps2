@echo off
echo ===================================================
echo     STARTING SUPEHDAH DEVELOPMENT ENVIRONMENT
echo ===================================================
echo.
echo This script will:
echo 1. Start the Laravel backend server
echo 2. Start the React Native development server
echo.

:: Check if PHP is installed
where php >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] PHP not found. Please ensure PHP is installed and in your PATH.
    exit /b 1
)

:: Check if Node.js is installed
where node >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] Node.js not found. Please ensure Node.js is installed and in your PATH.
    exit /b 1
)

:: Get the current directory
set CURRENT_DIR=%cd%

:: Laravel Backend
echo [INFO] Starting Laravel backend server...
cd supehdah

:: Clear caches
echo [INFO] Clearing Laravel caches...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize

:: Start Laravel server in a new terminal window
start powershell -NoExit -Command "php artisan serve --host=0.0.0.0"

echo [SUCCESS] Laravel backend started at http://localhost:8000

:: React Native App
echo [INFO] Starting React Native app...
cd ..\supehdah_mobile

:: Install any missing dependencies
echo [INFO] Checking node modules...
if not exist node_modules (
    echo [INFO] Installing node modules...
    npm install
)

:: Start the Metro bundler
echo [INFO] Starting Metro bundler...
start powershell -NoExit -Command "npm start"

echo [SUCCESS] Development environment started successfully!
echo.
echo Backend: http://localhost:8000
echo Mobile app: See Metro bundler window for connection options
echo.
echo Press any key to exit this script...
pause > nul

:: Return to the original directory
cd %CURRENT_DIR%
