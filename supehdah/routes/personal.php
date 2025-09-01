<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\PetController;

// User routes for pet management
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    // Pet management
    Route::get('/pets', [PetController::class, 'index'])->name('pets.index');
    Route::post('/pets', [PetController::class, 'store'])->name('pets.store');
    Route::put('/pets/{pet}', [PetController::class, 'update'])->name('pets.update');
    Route::delete('/pets/{pet}', [PetController::class, 'destroy'])->name('pets.destroy');
    
    // Pet vaccination records
    Route::post('/pets/{pet}/vaccinations', [PetController::class, 'storeVaccination'])->name('pet.vaccinations.store');
});
