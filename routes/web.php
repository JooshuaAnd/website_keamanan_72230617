<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FastAPIAuthController;

Route::get('/', function () {
    return view('welcome');
});

// FastAPI Auth Routes
Route::get('/login', [FastAPIAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [FastAPIAuthController::class, 'login']);

Route::get('/register', [FastAPIAuthController::class, 'showRegister'])->name('register');
Route::post('/register', [FastAPIAuthController::class, 'register']);

Route::get('/dashboard', [FastAPIAuthController::class, 'dashboard'])->name('dashboard');
Route::post('/logout', [FastAPIAuthController::class, 'logout'])->name('logout');

Route::get('/verify/{token}', [FastAPIAuthController::class, 'verify'])->name('verify');

Route::get('/forgot-password', [FastAPIAuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [FastAPIAuthController::class, 'forgotPassword'])->name('password.email');

Route::get('/reset-password', [FastAPIAuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [FastAPIAuthController::class, 'resetPassword'])->name('password.update');
