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

        // Sync Main account balance
        Account::syncMainAccountBalance();

        return redirect()->route('dashboard')->with('success', 'Transaction recorded successfully!');
    }

    /**
     * Remove the specified transaction and reverse balance changes.
     */
    public function destroy(Transaction $transaction)
    {
        $account = $transaction->account;

        // Reverse the balance change
        if ($transaction->type === 'income') {
            // If it was income, subtract it back
            $account->addExpense($transaction->amount);
        } else {
            // If it was expense, add it back
            $account->addIncome($transaction->amount);
        }

        // Delete the transaction
        $transaction->delete();

        // Sync Main account balance
        Account::syncMainAccountBalance();

        return back()->with('success', 'Transaction deleted and balance restored successfully!');
    }
}
