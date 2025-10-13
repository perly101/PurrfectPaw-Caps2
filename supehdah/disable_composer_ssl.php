<?php

// This script disables SSL verification for Composer
// WARNING: This is only for development environments. Not recommended for production.

$composerHome = getenv('COMPOSER_HOME');
if (!$composerHome) {
    // On Windows, this is typically in %APPDATA%\Composer
    $composerHome = getenv('APPDATA') . '/Composer';
}

$configFile = $composerHome . '/config.json';

// Create or update the config.json file
$config = file_exists($configFile) ? json_decode(file_get_contents($configFile), true) : [];

// Ensure the config array is properly structured
if (!isset($config['config'])) {
    $config['config'] = [];
}

// Set secure-http to false to bypass SSL verification
$config['config']['secure-http'] = false;
$config['config']['disable-tls'] = true;

// Save the updated config
if (!file_exists(dirname($configFile))) {
    mkdir(dirname($configFile), 0755, true);
}
file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "Composer SSL verification disabled successfully.\n";
echo "Config file location: $configFile\n";
echo "\nWARNING: This is only for development environments. Re-enable SSL for production.\n";
echo "\nNow you can run: composer require laravel/socialite --ignore-platform-reqs\n";
?>