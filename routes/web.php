<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FastAPIAuthController;
use App\Http\Controllers\LMSController;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Auth routes
Route::get('/login', [FastAPIAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [FastAPIAuthController::class, 'login'])->middleware('throttle:login');

Route::get('/register', [FastAPIAuthController::class, 'showRegister'])->name('register');
Route::post('/register', [FastAPIAuthController::class, 'register'])->middleware('throttle:register');

Route::post('/logout', [FastAPIAuthController::class, 'logout'])->name('logout');

Route::get('/verify/{token}', [FastAPIAuthController::class, 'verify'])->name('verify');
Route::get('/verify', [FastAPIAuthController::class, 'showCleanVerify'])->name('verify.clean');
Route::post('/verify', [FastAPIAuthController::class, 'verifyConfirm'])->name('verify.confirm');

Route::get('/forgot-password', [FastAPIAuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [FastAPIAuthController::class, 'forgotPassword'])->name('password.email');

Route::get('/reset-password/{token}', [FastAPIAuthController::class, 'resetPasswordLink'])->name('password.reset');
Route::get('/reset-password', [FastAPIAuthController::class, 'showResetPassword'])->name('password.reset.clean');
Route::post('/reset-password', [FastAPIAuthController::class, 'resetPassword'])->name('password.update');

// Dashboard (role-based redirect)
Route::get('/dashboard', [FastAPIAuthController::class, 'dashboard'])->name('dashboard');

// ============ ADMIN ROUTES ============
Route::middleware(['auth.session', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [LMSController::class, 'adminDashboard'])->name('dashboard');
    Route::get('/participants', [LMSController::class, 'adminParticipants'])->name('participants');
    Route::get('/lecturers', [LMSController::class, 'adminLecturers'])->name('lecturers');
    Route::get('/materials', [LMSController::class, 'adminMaterials'])->name('materials');

    // Search AJAX
    Route::get('/participants/search', [LMSController::class, 'searchParticipants'])->name('participants.search');
    Route::get('/lecturers/search', [LMSController::class, 'searchLecturers'])->name('lecturers.search');
    
    // Delete action
    Route::delete('/participants/{id}', [LMSController::class, 'deleteParticipant'])->name('participants.delete');
});

// ============ DOSEN ROUTES ============
Route::middleware(['auth.session', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/dashboard', [LMSController::class, 'dosenDashboard'])->name('dashboard');
    Route::get('/materials', [LMSController::class, 'dosenMaterials'])->name('materials');
    Route::get('/upload', [LMSController::class, 'dosenUpload'])->name('upload');
    Route::post('/upload', [LMSController::class, 'dosenUploadStore'])->name('upload.store');
});

// ============ PESERTA ROUTES ============
Route::middleware(['auth.session', 'role:peserta'])->prefix('peserta')->name('peserta.')->group(function () {
    Route::get('/dashboard', [LMSController::class, 'pesertaDashboard'])->name('dashboard');
    Route::get('/search', [LMSController::class, 'pesertaSearch'])->name('search');
    Route::get('/search/results', [LMSController::class, 'pesertaSearchResults'])->name('search.results');
});

// ============ GENERAL AUTH ROUTES ============
Route::middleware(['auth.session'])->group(function () {
    Route::get('/profile', [LMSController::class, 'profile'])->name('profile');
});

Route::post('/csrf-test', function () {
    return 'CSRF lolos';
});
