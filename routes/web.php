<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CostEstimationController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Onboarding routes
Route::get('/onboarding', [AccountController::class, 'onboarding'])->name('onboarding');
Route::post('/onboarding', [AccountController::class, 'storeOnboarding'])->name('onboarding.store');

// Account routes
Route::resource('accounts', AccountController::class);
Route::post('/settings/exchange-rate', [AccountController::class, 'updateExchangeRate'])->name('settings.exchange-rate');

// Chat/Transaction routes
Route::post('/chat/parse', [ChatController::class, 'parse'])->name('chat.parse');
Route::post('/chat/transfer', [ChatController::class, 'transfer'])->name('chat.transfer');
Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

// Transaction history
Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

// Cost estimation routes
Route::get('/cost-estimations', [CostEstimationController::class, 'index'])->name('cost-estimations.index');
Route::post('/cost-estimations/parse', [CostEstimationController::class, 'parse'])->name('cost-estimations.parse');
Route::delete('/cost-estimations/{costEstimation}', [CostEstimationController::class, 'destroy'])->name('cost-estimations.destroy');

// Temporary route to sync Main account (can be removed later)
Route::get('/sync-main-account', function() {
    \App\Models\Account::syncMainAccountBalance();
    $mainAccount = \App\Models\Account::where('name', 'Main')->first();
    return redirect()->route('dashboard')->with('success', 'Main account synced! New balance: ৳' . number_format($mainAccount->balance, 2));
});
