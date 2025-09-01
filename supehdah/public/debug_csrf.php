<?php
// Turn on verbose error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include the Laravel bootstrap file
require __DIR__ . '/bootstrap/app.php';

// Use the Laravel CSRF token generator
$token = csrf_token();
echo "CSRF Token: " . $token . "\n";
