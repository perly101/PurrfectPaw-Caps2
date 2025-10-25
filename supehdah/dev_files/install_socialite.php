<?php

// This script manually installs Laravel Socialite by extracting it directly 
// from the GitHub repository and updating composer.json

// Define the repository URL and version
$repository = 'https://github.com/laravel/socialite';
$version = 'v5.8.0'; // Using a recent stable version
$zipUrl = "{$repository}/archive/refs/tags/{$version}.zip";
$destinationDir = __DIR__ . '/vendor/laravel/socialite';

// Create directories if they don't exist
if (!is_dir(dirname($destinationDir))) {
    mkdir(dirname($destinationDir), 0755, true);
}
if (is_dir($destinationDir)) {
    echo "Removing existing Socialite directory...\n";
    // Remove directory recursively
    $iterator = new RecursiveDirectoryIterator($destinationDir, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
        if ($file->isDir()) {
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }
    rmdir($destinationDir);
}

// Create temporary file
$zipFile = tempnam(sys_get_temp_dir(), 'socialite');
echo "Downloading Laravel Socialite from GitHub...\n";

// Set up cURL options to download without SSL verification
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $zipUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$data = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch) . "\n";
    exit(1);
}

curl_close($ch);

// Save to temp file
file_put_contents($zipFile, $data);
echo "Downloaded to temporary file: {$zipFile}\n";

// Extract the ZIP file
echo "Extracting ZIP file...\n";
$zip = new ZipArchive;
if ($zip->open($zipFile) === true) {
    $extractDir = sys_get_temp_dir() . '/socialite-extract';
    if (is_dir($extractDir)) {
        $iterator = new RecursiveDirectoryIterator($extractDir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($extractDir);
    }
    mkdir($extractDir);
    
    $zip->extractTo($extractDir);
    $zip->close();
    
    // Move the extracted files to the vendor directory
    echo "Moving files to {$destinationDir}...\n";
    $extractedDir = glob($extractDir . '/socialite-*')[0] ?? null;
    
    if ($extractedDir && is_dir($extractedDir)) {
        mkdir($destinationDir, 0755, true);
        
        // Copy directory contents recursively
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($extractedDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            $target = $destinationDir . DIRECTORY_SEPARATOR . $iterator->getSubPathname();
            if ($item->isDir()) {
                if (!is_dir($target)) {
                    mkdir($target, 0755, true);
                }
            } else {
                copy($item, $target);
            }
        }
    } else {
        echo "Error: Could not find extracted directory.\n";
        exit(1);
    }
} else {
    echo "Error: Could not extract ZIP file.\n";
    exit(1);
}

// Clean up
echo "Cleaning up temporary files...\n";
unlink($zipFile);
$iterator = new RecursiveDirectoryIterator($extractDir, RecursiveDirectoryIterator::SKIP_DOTS);
$files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);
foreach ($files as $file) {
    if ($file->isDir()) {
        rmdir($file->getRealPath());
    } else {
        unlink($file->getRealPath());
    }
}
rmdir($extractDir);

// Update composer.json to include Socialite
echo "Updating composer.json...\n";
$composerJson = json_decode(file_get_contents(__DIR__ . '/composer.json'), true);
if (!isset($composerJson['require']['laravel/socialite'])) {
    $composerJson['require']['laravel/socialite'] = '^5.8.0';
    file_put_contents(__DIR__ . '/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

// Create a simple class map autoloader for Socialite
echo "Creating autoload file for Socialite...\n";
$autoloadFile = __DIR__ . '/vendor/laravel/socialite/autoload.php';
file_put_contents($autoloadFile, '<?php

// Auto-generated autoload file for Laravel Socialite
require_once __DIR__ . "/src/SocialiteServiceProvider.php";
require_once __DIR__ . "/src/Facades/Socialite.php";
require_once __DIR__ . "/src/Contracts/Provider.php";
require_once __DIR__ . "/src/Contracts/User.php";
require_once __DIR__ . "/src/Two/AbstractProvider.php";
require_once __DIR__ . "/src/Two/GoogleProvider.php";
require_once __DIR__ . "/src/Two/User.php";
');

// Create an entry in the autoload_psr4.php file
$autoloadPsr4File = __DIR__ . '/vendor/composer/autoload_psr4.php';
if (file_exists($autoloadPsr4File)) {
    echo "Adding Socialite to PSR-4 autoloader...\n";
    $autoloadPsr4 = file_get_contents($autoloadPsr4File);
    if (strpos($autoloadPsr4, "'Laravel\\\\Socialite\\\\' => ") === false) {
        $autoloadPsr4 = str_replace('return array(', "return array(\n    'Laravel\\\\Socialite\\\\' => array(\$vendorDir . '/laravel/socialite/src'),", $autoloadPsr4);
        file_put_contents($autoloadPsr4File, $autoloadPsr4);
    }
}

// Update the autoload_static.php file
$autoloadStaticFile = __DIR__ . '/vendor/composer/autoload_static.php';
if (file_exists($autoloadStaticFile)) {
    echo "Adding Socialite to static autoloader...\n";
    $autoloadStatic = file_get_contents($autoloadStaticFile);
    if (strpos($autoloadStatic, "'Laravel\\\\Socialite\\\\' =>") === false) {
        $autoloadStatic = str_replace('public static $prefixLengthsPsr4 = array(', "public static \$prefixLengthsPsr4 = array(\n        'Laravel\\\\Socialite\\\\' => 19,", $autoloadStatic);
        $autoloadStatic = str_replace('public static $prefixDirsPsr4 = array(', "public static \$prefixDirsPsr4 = array(\n        'Laravel\\\\Socialite\\\\' => array(0 => \$vendorDir . '/laravel/socialite/src'),", $autoloadStatic);
        file_put_contents($autoloadStaticFile, $autoloadStatic);
    }
}

echo "\n===================================================\n";
echo "Laravel Socialite installed successfully!\n";
echo "Please run the following command to register the provider:\n";
echo "php artisan vendor:publish --provider=\"Laravel\\\Socialite\\\SocialiteServiceProvider\"\n";
echo "Then run: php artisan optimize:clear\n";
echo "===================================================\n";
?>