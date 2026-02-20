<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Services\ChatParserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    protected ChatParserService $chatParser;

    public function __construct(ChatParserService $chatParser)
    {
        $this->chatParser = $chatParser;
    }

    /**
     * Parse natural language input and return parsed data.
     */
    public function parse(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $result = $this->chatParser->parse($request->message);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Transaction parsed successfully!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'errors' => $result['errors'],
                'data' => $result
            ], 422);
        }
    }

    /**
     * Process and create a transfer transaction.
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id|different:from_account_id',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        $fromAccount = Account::findOrFail($request->from_account_id);
        $toAccount = Account::findOrFail($request->to_account_id);

        // Validate sufficient balance
        if ($request->amount > $fromAccount->balance) {
            return back()->with('error', 'Insufficient balance in ' . $fromAccount->name . '!');
        }

        try {
            DB::beginTransaction();

            // Deduct from source account
            $fromAccount->addExpense($request->amount);

            // Add to destination account
            $toAccount->addIncome($request->amount);

            // Create transaction records for both
            Transaction::create([
                'account_id' => $fromAccount->id,
                'type' => 'expense',
                'amount' => $request->amount,
                'note' => $request->note . ' (Transfer to ' . $toAccount->name . ')',
            ]);

            Transaction::create([
                'account_id' => $toAccount->id,
                'type' => 'income',
                'amount' => $request->amount,
                'note' => $request->note . ' (Transfer from ' . $fromAccount->name . ')',
            ]);

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Transfer completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Transfer failed: ' . $e->getMessage());
        }
    }
}
