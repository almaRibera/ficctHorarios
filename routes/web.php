<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\CheckRole;


// Rutas de autenticación
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

// Ruta principal
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->rol == 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('docente.dashboard');
    }
    return redirect()->route('login');
});

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Dashboard admin
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard')->middleware('role:admin');

    // Dashboard docente
    Route::get('/docente/dashboard', function () {
        return view('docente.dashboard');
    })->name('docente.dashboard')->middleware('role:docente');

    // Rutas de gestión de usuarios (solo admin)
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
    });
});