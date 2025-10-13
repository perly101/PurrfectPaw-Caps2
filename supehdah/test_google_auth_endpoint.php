<?php

// Test the Google Mobile Auth endpoint

// Ensure PHP exceptions show up
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$apiUrl = "http://localhost:8000/api/auth/google/callback-mobile";

// Create a test payload
$payload = [
    'id_token' => 'test_id_token',  // Dummy token for testing endpoint availability
    'access_token' => 'test_access_token'
];

// Initialize cURL
$ch = curl_init($apiUrl);

// Set request options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check for errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
} else {
    echo "HTTP Status Code: " . $httpCode . "\n";
    echo "Response Body:\n" . $response . "\n";
}

curl_close($ch);

echo "\nFinished testing endpoint.";