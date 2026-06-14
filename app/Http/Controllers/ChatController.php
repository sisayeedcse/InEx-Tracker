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
     * Rate-limited to 10 requests per minute via route middleware.
     */
    public function parse(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        // Sanitize input
        $message = strip_tags(trim($request->message));
        $message = mb_substr($message, 0, 500);

        $result = $this->chatParser->parse($message);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'data'    => $result,
                'message' => 'Transaction parsed successfully!',
            ]);
        }

        return response()->json([
            'success' => false,
            'errors'  => $result['errors'],
            'data'    => $result,
        ], 422);
    }

    /**
     * Process and create a transfer transaction.
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id'   => 'required|exists:accounts,id|different:from_account_id',
            'amount'          => 'required|numeric|min:0.01',
            'note'            => 'nullable|string|max:500',
        ]);

        $fromAccount = Account::findOrFail($request->from_account_id);
        $toAccount   = Account::findOrFail($request->to_account_id);

        if ($request->amount > $fromAccount->balance) {
            return back()->with('error', 'Insufficient balance in ' . $fromAccount->name . '!');
        }

        try {
            DB::beginTransaction();

            $fromAccount->addExpense($request->amount);
            $toAccount->addIncome($request->amount);

            Transaction::create([
                'account_id' => $fromAccount->id,
                'type'       => 'expense',
                'amount'     => $request->amount,
                'note'       => ($request->note ?? 'Transfer') . ' (Transfer to ' . $toAccount->name . ')',
            ]);

            Transaction::create([
                'account_id' => $toAccount->id,
                'type'       => 'income',
                'amount'     => $request->amount,
                'note'       => ($request->note ?? 'Transfer') . ' (Transfer from ' . $fromAccount->name . ')',
            ]);

            DB::commit();
            Account::syncMainAccountBalance();

            return redirect()->route('dashboard')->with('success', 'Transfer of ৳' . number_format($request->amount, 2) . ' completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Transfer failed: ' . $e->getMessage());
        }
    }
}
