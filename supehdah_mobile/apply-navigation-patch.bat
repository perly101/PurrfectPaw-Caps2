@echo off
echo Applying React Navigation patch for web...

set NODE_MODULES=node_modules
set TARGET_DIR=%NODE_MODULES%\@react-navigation\elements\lib\module
set PATCH_FILE=navigation-patch\useFrameSize.js

if not exist "%TARGET_DIR%" (
  echo Error: Target directory not found at %TARGET_DIR%
  echo Make sure you have installed the dependencies first.
  exit /b 1
)

echo Creating backup of original file...
if exist "%TARGET_DIR%\useFrameSize.js" (
  copy "%TARGET_DIR%\useFrameSize.js" "%TARGET_DIR%\useFrameSize.js.bak"
)

echo Copying patched file...
copy "%PATCH_FILE%" "%TARGET_DIR%\useFrameSize.js"

if %ERRORLEVEL% neq 0 (
  echo Failed to apply patch.
  exit /b 1
)

echo Patch applied successfully!
echo Run 'npx expo start --web' to start the app.