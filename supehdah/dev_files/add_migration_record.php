<?php

// add_migration_record.php
require_once __DIR__ . '/vendor/autoload.php';

// Load the .env file
$dotenv = \Illuminate\Support\Env::get('APP_ENV');
if (file_exists(__DIR__ . '/.env')) {
    \Dotenv\Dotenv::createImmutable(__DIR__)->load();
}

// Get the database connection details from the .env file
$connection = [
    'host'      => env('DB_HOST', '127.0.0.1'),
    'port'      => env('DB_PORT', '3306'),
    'database'  => env('DB_DATABASE', 'forge'),
    'username'  => env('DB_USERNAME', 'forge'),
    'password'  => env('DB_PASSWORD', '')
];

try {
    // Connect to the database
    $pdo = new PDO(
        "mysql:host={$connection['host']};port={$connection['port']};dbname={$connection['database']}",
        $connection['username'],
        $connection['password']
    );
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get the current max batch number
    $stmt = $pdo->query("SELECT MAX(batch) as max_batch FROM migrations");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $batch = $result['max_batch'] + 1;
    
    // Insert the migration record
    $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
    $stmt->execute(['2023_10_25_000000_create_subscriptions_table', $batch]);
    
    echo "Migration record has been successfully added!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}