<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions with filtering.
     */
    public function index(Request $request)
    {
        $query = Transaction::with('account')->latest();

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }
        if ($request->filled('keyword')) {
            $query->search($request->keyword);
        }

        // CSV export
        if ($request->boolean('export')) {
            return $this->exportCsv($query->get());
        }

        $transactions = $query->paginate(20)->withQueryString();
        $accounts     = Account::all();

        // Summary for filtered set
        $filtered   = $query->get();
        $sumIncome  = $filtered->where('type', 'income')->sum('amount');
        $sumExpense = $filtered->where('type', 'expense')->sum('amount');

        return view('transactions.index', compact(
            'transactions',
            'accounts',
            'sumIncome',
            'sumExpense'
        ));
    }

    /**
     * Store a newly created transaction.
     */
    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'type'       => 'required|in:income,expense',
            'amount'     => 'required|numeric|min:0.01',
            'note'       => 'nullable|string|max:500',
        ]);

        $account = Account::findOrFail($request->account_id);

        if ($request->type === 'expense' && $request->amount > $account->balance) {
            return back()->with('error', 'Insufficient balance in ' . $account->name . '!');
        }

        Transaction::create([
            'account_id' => $request->account_id,
            'type'       => $request->type,
            'amount'     => $request->amount,
            'note'       => $request->note,
        ]);

        if ($request->type === 'income') {
            $account->addIncome($request->amount);
        } else {
            $account->addExpense($request->amount);
        }

        Account::syncMainAccountBalance();

        return redirect()->route('dashboard')->with('success', 'Transaction recorded successfully!');
    }

    /**
     * Remove the specified transaction and reverse balance changes.
     */
    public function destroy(Transaction $transaction)
    {
        $account = $transaction->account;

        if ($transaction->type === 'income') {
            $account->addExpense($transaction->amount);
        } else {
            $account->addIncome($transaction->amount);
        }

        $transaction->delete();
        Account::syncMainAccountBalance();

        return back()->with('success', 'Transaction deleted and balance restored.');
    }

    /**
     * Export transactions as CSV.
     */
    private function exportCsv($transactions)
    {
        $filename = 'transactions-' . now()->format('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Date', 'Type', 'Account', 'Amount (BDT)', 'Note']);

            foreach ($transactions as $t) {
                fputcsv($handle, [
                    $t->id,
                    $t->created_at->format('Y-m-d H:i'),
                    ucfirst($t->type),
                    $t->account->name ?? 'N/A',
                    number_format($t->amount, 2, '.', ''),
                    $t->note ?? '',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
