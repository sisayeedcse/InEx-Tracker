<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Setting;

class DashboardController extends Controller
{
    public function index()
    {
        // Redirect to onboarding if no accounts yet
        if (Account::count() === 0) {
            return redirect()->route('onboarding');
        }

        $accounts    = Account::all();
        $totalBalance = Account::excludeMain()->sum('balance');

        $recentTransactions = Transaction::with('account')
            ->latest()
            ->take(8)
            ->get();

        // All-time totals
        $totalIncome  = Transaction::income()->sum('amount');
        $totalExpense = Transaction::expense()->sum('amount');

        // This month
        $monthIncome  = Transaction::income()->thisMonth()->sum('amount');
        $monthExpense = Transaction::expense()->thisMonth()->sum('amount');

        // 6-month chart data
        $monthlyData = Transaction::monthlyTotals(6);

        $usdToBdtRate = Setting::getUsdToBdtRate();

        return view('dashboard', compact(
            'accounts',
            'totalBalance',
            'recentTransactions',
            'totalIncome',
            'totalExpense',
            'monthIncome',
            'monthExpense',
            'monthlyData',
            'usdToBdtRate'
        ));
    }
}
