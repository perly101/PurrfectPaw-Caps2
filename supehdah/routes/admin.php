<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FakePaymentController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application.
|
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Routes moved to web.php to fix navigation issues
});
