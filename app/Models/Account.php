<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = [
        'name',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    /**
     * Get all transactions for this account.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Add amount to balance (income).
     */
    public function addIncome(float $amount): void
    {
        $this->increment('balance', $amount);
    }

    /**
     * Subtract amount from balance (expense).
     */
    public function addExpense(float $amount): void
    {
        $this->decrement('balance', $amount);
    }
}
