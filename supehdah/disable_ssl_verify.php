<?php

// This script will be included in public/index.php to globally disable SSL verification
// WARNING: This is only for development/testing. DO NOT use in production!

// Disable SSL verification globally for PHP streams
ini_set('openssl.cafile', '');
ini_set('openssl.capath', '');

// Disable SSL verification for stream contexts
stream_context_set_default([
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    ]
]);