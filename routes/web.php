<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Onboarding routes
Route::get('/onboarding', [AccountController::class, 'onboarding'])->name('onboarding');
Route::post('/onboarding', [AccountController::class, 'storeOnboarding'])->name('onboarding.store');

// Account routes
Route::resource('accounts', AccountController::class);

// Chat/Transaction routes
Route::post('/chat/parse', [ChatController::class, 'parse'])->name('chat.parse');
Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');

// Transaction history
Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
