<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CostEstimationController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Onboarding
Route::get('/onboarding',  [AccountController::class, 'onboarding'])->name('onboarding');
Route::post('/onboarding', [AccountController::class, 'storeOnboarding'])->name('onboarding.store');

// Accounts
Route::resource('accounts', AccountController::class);
Route::post('/settings/exchange-rate', [AccountController::class, 'updateExchangeRate'])->name('settings.exchange-rate');

// AI Chat / Transaction Parser — rate limited to 10 requests/minute
Route::post('/chat/parse',    [ChatController::class, 'parse'])->name('chat.parse')->middleware('throttle:10,1');
Route::post('/chat/transfer', [ChatController::class, 'transfer'])->name('chat.transfer');

// Transactions (store + index + destroy)
Route::post('/transactions',              [TransactionController::class, 'store'])->name('transactions.store');
Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
Route::get('/transactions',              [TransactionController::class, 'index'])->name('transactions.index');

// Cost Estimations
Route::get('/cost-estimations',          [CostEstimationController::class, 'index'])->name('cost-estimations.index');
Route::post('/cost-estimations/parse',   [CostEstimationController::class, 'parse'])->name('cost-estimations.parse');
Route::delete('/cost-estimations/{costEstimation}', [CostEstimationController::class, 'destroy'])->name('cost-estimations.destroy');

// Sync Main account (utility, can be removed in production)
Route::get('/sync-main-account', function () {
    \App\Models\Account::syncMainAccountBalance();
    $main = \App\Models\Account::onlyMain()->first();
    return redirect()->route('dashboard')->with('success', 'Main account synced! Balance: ৳' . number_format($main->balance, 2));
});
