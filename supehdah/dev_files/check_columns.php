<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$columns = \Illuminate\Support\Facades\Schema::getColumnListing('appointments');
print_r($columns);
