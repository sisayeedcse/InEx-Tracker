<?php

namespace App\Services;

use App\Models\Account;

class ChatParserService
{
    /**
     * Income keywords
     */
    private const INCOME_KEYWORDS = [
        'received', 'got', 'earned', 'had', 'income', 'salary', 'payment', 'credited'
    ];

    /**
     * Expense keywords
     */
    private const EXPENSE_KEYWORDS = [
        'bought', 'paid', 'spent', 'purchased', 'gave', 'send', 'sent', 'transferred', 
        'expense', 'cost', 'debited', 'withdraw', 'withdrawn'
    ];

    /**
     * Parse natural language input and extract transaction details.
     *
     * @param string $input
     * @return array
     */
    public function parse(string $input): array
    {
        $result = [
            'success' => false,
            'type' => null,
            'amount' => null,
            'account' => null,
            'account_id' => null,
            'note' => $input,
            'errors' => []
        ];

        // Extract amount
        $amount = $this->extractAmount($input);
        if (!$amount) {
            $result['errors'][] = 'Could not detect amount in the message.';
        } else {
            $result['amount'] = $amount;
        }

        // Detect transaction type
        $type = $this->detectType($input);
        if (!$type) {
            $result['errors'][] = 'Could not determine if this is income or expense.';
        } else {
            $result['type'] = $type;
        }

        // Detect account
        $account = $this->detectAccount($input);
        if (!$account) {
            $result['errors'][] = 'Could not identify the account. Please mention one of your accounts.';
        } else {
            $result['account'] = $account->name;
            $result['account_id'] = $account->id;
        }

        // Mark as successful if no errors
        if (empty($result['errors'])) {
            $result['success'] = true;
        }

        return $result;
    }

    /**
     * Extract numeric amount from input string.
     *
     * @param string $input
     * @return float|null
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
     * Detect transaction type (income or expense) based on keywords.
     *
     * @param string $input
     * @return string|null
     */
    private function detectType(string $input): ?string
    {
        $lowerInput = strtolower($input);

        // Check for income keywords
        foreach (self::INCOME_KEYWORDS as $keyword) {
            if (str_contains($lowerInput, strtolower($keyword))) {
                return 'income';
            }
        }

        // Check for expense keywords
        foreach (self::EXPENSE_KEYWORDS as $keyword) {
            if (str_contains($lowerInput, strtolower($keyword))) {
                return 'expense';
            }
        }

        return null;
    }

    /**
     * Detect account by matching account names in input.
     *
     * @param string $input
     * @return Account|null
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

    /**
     * Validate if expense can be processed (balance check).
     *
     * @param Account $account
     * @param float $amount
     * @return bool
     */
    public function canProcessExpense(Account $account, float $amount): bool
    {
        return $account->balance >= $amount;
    }
}
