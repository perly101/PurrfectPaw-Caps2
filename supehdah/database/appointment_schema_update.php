<?php
/**
 * Database schema update script for appointments table
 * 
 * This script ensures the appointments table has all required columns for proper mobile integration
 * Run this script directly by navigating to its URL or from the command line
 */

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Create or update appointments table
if (!Schema::hasTable('appointments')) {
    try {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinic_id');
            $table->string('owner_name');
            $table->string('owner_phone');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->string('status')->default('pending');
            $table->timestamps();
            
            $table->foreign('clinic_id')->references('id')->on('clinic_info')->onDelete('cascade');
        });
        echo "Successfully created appointments table.\n";
    } catch (\Exception $e) {
        echo "Error creating appointments table: " . $e->getMessage() . "\n";
    }
} else {
    // Table exists, check for and add any missing columns
    $requiredColumns = [
        'clinic_id' => 'unsignedBigInteger',
        'owner_name' => 'string',
        'owner_phone' => 'string',
        'appointment_date' => 'date',
        'appointment_time' => 'time',
        'status' => 'string'
    ];
    
    $columns = Schema::getColumnListing('appointments');
    $missingColumns = array_diff(array_keys($requiredColumns), $columns);
    
    if (!empty($missingColumns)) {
        try {
            Schema::table('appointments', function (Blueprint $table) use ($missingColumns, $requiredColumns) {
                foreach ($missingColumns as $column) {
                    $type = $requiredColumns[$column];
                    switch ($type) {
                        case 'unsignedBigInteger':
                            $table->unsignedBigInteger($column);
                            break;
                        case 'string':
                            $table->string($column)->nullable();
                            break;
                        case 'date':
                            $table->date($column)->nullable();
                            break;
                        case 'time':
                            $table->time($column)->nullable();
                            break;
                        default:
                            $table->string($column)->nullable();
                    }
                }
            });
            echo "Successfully added missing columns to appointments table: " . implode(', ', $missingColumns) . "\n";
        } catch (\Exception $e) {
            echo "Error adding columns to appointments table: " . $e->getMessage() . "\n";
        }
    } else {
        echo "All required columns already exist in appointments table.\n";
    }
}

// Create appointments_responses table if it doesn't exist
if (!Schema::hasTable('appointment_responses')) {
    try {
        Schema::create('appointment_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appointment_id');
            $table->string('field_id');
            $table->text('value')->nullable();
            $table->timestamps();
            
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');
        });
        echo "Successfully created appointment_responses table.\n";
    } catch (\Exception $e) {
        echo "Error creating appointment_responses table: " . $e->getMessage() . "\n";
    }
} else {
    echo "Appointment_responses table already exists.\n";
}

echo "Schema update completed.\n";
