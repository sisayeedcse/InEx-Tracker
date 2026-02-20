<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Setting;

class DashboardController extends Controller
{
    public function index()
    {
        // Check if onboarding is complete
        if (Account::count() === 0) {
            return redirect()->route('onboarding');
        }

        $accounts = Account::all();
        // Exclude Main account from total to avoid duplication
        $totalBalance = Account::where('name', '!=', 'Main')->sum('balance');
        $recentTransactions = Transaction::with('account')
            ->latest()
            ->take(10)
            ->get();

        // Statistics
        $totalIncome = Transaction::where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('type', 'expense')->sum('amount');
        $usdToBdtRate = Setting::getUsdToBdtRate();

        return view('dashboard', compact(
            'accounts',
            'totalBalance',
            'recentTransactions',
            'totalIncome',
            'totalExpense',
            'usdToBdtRate'
        ));
    }
}
