<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CostEstimation;
use App\Services\ChatParserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CostEstimationController extends Controller
{
    private ChatParserService $chatParser;

    public function __construct(ChatParserService $chatParser)
    {
        $this->chatParser = $chatParser;
    }

    /**
     * Display the cost estimation page.
     */
    public function index()
    {
        $accounts = Account::all();
        $costEstimations = CostEstimation::with('account')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate total current balance across all accounts
        $totalCurrentBalance = $accounts->sum('balance');

        // Calculate total estimated costs
        $totalEstimatedCosts = $costEstimations->sum('amount');

        // Calculate projected balance
        $projectedBalance = $totalCurrentBalance - $totalEstimatedCosts;

        return view('cost-estimations.index', compact(
            'accounts',
            'costEstimations',
            'totalCurrentBalance',
            'totalEstimatedCosts',
            'projectedBalance'
        ));
    }

    /**
     * Parse and store a new cost estimation.
     */
    public function parse(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $input = $request->input('message');

        // Extract amount from the message
        $amount = $this->extractAmount($input);
        
        if (!$amount) {
            return back()->with('error', 'Could not detect amount in the message.');
        }

        // Detect account
        $account = $this->detectAccount($input);
        
        if (!$account) {
            // Use default account (first account) if no specific account mentioned
            $account = Account::first();
            
            if (!$account) {
                return back()->with('error', 'No account found. Please create an account first.');
            }
        }

        // Create cost estimation
        CostEstimation::create([
            'account_id' => $account->id,
            'amount' => $amount,
            'description' => $input,
        ]);

        return back()->with('success', 'Cost estimation added successfully!');
    }

    /**
     * Delete a cost estimation.
     */
    public function destroy(CostEstimation $costEstimation)
    {
        $costEstimation->delete();

        return back()->with('success', 'Cost estimation deleted successfully!');
    }

    /**
     * Extract numeric amount from input string.
     */
    private function extractAmount(string $input): ?float
    {
        // Match numbers (including decimals)
        if (preg_match('/\b(\d+(?:[.,]\d+)?)\b/', $input, $matches)) {
            // Replace comma with dot for decimal parsing
            $amount = str_replace(',', '.', $matches[1]);
            return (float) $amount;
        }

        return null;
    }

    /**
     * Detect account from input string.
     */
    private function detectAccount(string $input): ?Account
    {
        $accounts = Account::all();
        $lowerInput = strtolower($input);

        foreach ($accounts as $account) {
            $accountName = strtolower($account->name);
            
            // Check if account name appears in the input
            if (str_contains($lowerInput, $accountName)) {
                return $account;
            }
        }

        return null;
    }
}
