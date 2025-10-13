<?php
// This script checks if the password column in users table is nullable

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check if password is nullable
$passwordColumn = DB::selectOne("SHOW COLUMNS FROM users LIKE 'password'");
echo "Password column nullable: " . ($passwordColumn->Null === 'YES' ? 'YES' : 'NO') . "\n";