<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions.
     */
    public function index(Request $request)
    {
        $query = Transaction::with('account')->latest();

        // Filter by account
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->paginate(20);
        $accounts = Account::all();

        return view('transactions.index', compact('transactions', 'accounts'));
    }

    /**
     * Store a newly created transaction.
     */
    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        $account = Account::findOrFail($request->account_id);

        // Validate expense doesn't exceed balance (optional)
        if ($request->type === 'expense' && $request->amount > $account->balance) {
            return back()->with('error', 'Insufficient balance!');
        }

        // Create transaction
        $transaction = Transaction::create([
            'account_id' => $request->account_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'note' => $request->note,
        ]);

        // Update account balance
        if ($request->type === 'income') {
            $account->addIncome($request->amount);
        } else {
            $account->addExpense($request->amount);
        }

        return redirect()->route('dashboard')->with('success', 'Transaction recorded successfully!');
    }
}
