<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    $user=Auth::user();
    if($user)
        return redirect()->route('dashboard');
    else
        return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //TEMPORAL*********************************************
    Route::get('/nueva-cotizacion', function () {
        return view('cotizacion'); // crea un archivo nueva-cotizacion.blade.php
    });
});

require __DIR__.'/auth.php';
