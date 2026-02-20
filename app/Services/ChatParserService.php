<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Setting;

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
        'bought', 'paid', 'spent', 'purchased', 'gave', 'send', 'sent', 
        'expense', 'cost', 'debited', 'withdraw', 'withdrawn'
    ];

    /**
     * Transfer keywords
     */
    private const TRANSFER_KEYWORDS = [
        'transfer', 'transferred', 'move', 'moved', 'send', 'sent'
    ];

    /**
     * Transfer indicators (from/to patterns)
     */
    private const TRANSFER_PATTERNS = [
        'from\s+(\w+)\s+to\s+(\w+)',
        'to\s+(\w+)\s+from\s+(\w+)'
    ];

    /**
     * Parse natural language input and extract transaction details.
     *
     * @param string $input
     * @return array
     */
    public function parse(string $input): array
    {
        // First check if this is a transfer
        if ($this->isTransfer($input)) {
            return $this->parseTransfer($input);
        }

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
     * Check if input indicates a transfer transaction.
     *
     * @param string $input
     * @return bool
     */
    private function isTransfer(string $input): bool
    {
        $lowerInput = strtolower($input);

        // Check for transfer keywords
        foreach (self::TRANSFER_KEYWORDS as $keyword) {
            if (str_contains($lowerInput, $keyword)) {
                // Also check for from/to pattern indicators
                foreach (self::TRANSFER_PATTERNS as $pattern) {
                    if (preg_match('/' . $pattern . '/i', $input)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Parse transfer transaction.
     *
     * @param string $input
     * @return array
     */
    private function parseTransfer(string $input): array
    {
        $result = [
            'success' => false,
            'type' => 'transfer',
            'amount' => null,
            'from_account' => null,
            'from_account_id' => null,
            'to_account' => null,
            'to_account_id' => null,
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

        // Extract source and destination accounts
        $accounts = $this->extractTransferAccounts($input);
        
        if (!$accounts['from']) {
            $result['errors'][] = 'Could not identify the source account (from).';
        } else {
            $result['from_account'] = $accounts['from']->name;
            $result['from_account_id'] = $accounts['from']->id;
        }

        if (!$accounts['to']) {
            $result['errors'][] = 'Could not identify the destination account (to).';
        } else {
            $result['to_account'] = $accounts['to']->name;
            $result['to_account_id'] = $accounts['to']->id;
        }

        // Check if transferring to the same account
        if ($accounts['from'] && $accounts['to'] && $accounts['from']->id === $accounts['to']->id) {
            $result['errors'][] = 'Cannot transfer to the same account.';
        }

        // Check if source has sufficient balance
        if ($accounts['from'] && $amount && !$this->canProcessExpense($accounts['from'], $amount)) {
            $result['errors'][] = 'Insufficient balance in source account (' . $accounts['from']->name . ').';
        }

        // Mark as successful if no errors
        if (empty($result['errors'])) {
            $result['success'] = true;
        }

        return $result;
    }

    /**
     * Extract source and destination accounts from transfer input.
     *
     * @param string $input
     * @return array
     */
    private function extractTransferAccounts(string $input): array
    {
        $fromAccount = null;
        $toAccount = null;
        
        // Try pattern: "from X to Y"
        if (preg_match('/from\s+(\w+)\s+to\s+(\w+)/i', $input, $matches)) {
            $fromAccount = $this->detectAccountByName($matches[1]);
            $toAccount = $this->detectAccountByName($matches[2]);
        }
        // Try pattern: "to Y from X"
        elseif (preg_match('/to\s+(\w+)\s+from\s+(\w+)/i', $input, $matches)) {
            $toAccount = $this->detectAccountByName($matches[1]);
            $fromAccount = $this->detectAccountByName($matches[2]);
        }

        return [
            'from' => $fromAccount,
            'to' => $toAccount
        ];
    }

    /**
     * Detect account by specific name match.
     *
     * @param string $name
     * @return Account|null
     */
    private function detectAccountByName(string $name): ?Account
    {
        $accounts = Account::all();
        $lowerName = strtolower(trim($name));

        foreach ($accounts as $account) {
            $accountName = strtolower($account->name);
            
            // Exact or partial match
            if ($accountName === $lowerName || str_contains($accountName, $lowerName) || str_contains($lowerName, $accountName)) {
                return $account;
            }
        }

        return null;
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
            $amount = (float) $amount;

            // Check if the amount is in USD and convert to BDT
            if ($this->isUsdCurrency($input)) {
                $exchangeRate = Setting::getUsdToBdtRate();
                $amount = $amount * $exchangeRate;
            }

            return $amount;
        }

        return null;
    }

    /**
     * Check if the input mentions USD currency.
     *
     * @param string $input
     * @return bool
     */
    private function isUsdCurrency(string $input): bool
    {
        $lowerInput = strtolower($input);
        
        // Check for dollar keywords
        $usdKeywords = ['dollar', 'dollars', 'usd', '$'];
        
        foreach ($usdKeywords as $keyword) {
            if (str_contains($lowerInput, $keyword)) {
                return true;
            }
        }

        return false;
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
