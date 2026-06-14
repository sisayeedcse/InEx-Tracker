<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

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
     * Scope: exclude the virtual Main account.
     */
    public function scopeExcludeMain(Builder $query): Builder
    {
        return $query->where('name', '!=', 'Main');
    }

    /**
     * Scope: only the Main account.
     */
    public function scopeOnlyMain(Builder $query): Builder
    {
        return $query->where('name', 'Main');
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

    /**
     * Sync Main account balance with sum of all other accounts.
     */
    public static function syncMainAccountBalance(): void
    {
        $mainAccount = self::onlyMain()->first();

        if ($mainAccount) {
            $totalBalance = self::excludeMain()->sum('balance');
            $mainAccount->update(['balance' => $totalBalance]);
        }
    }

    /**
     * Whether this account holds USD (Payoneer-specific).
     */
    public function isUsdAccount(): bool
    {
        return strtolower($this->name) === 'payoneer';
    }

    /**
     * Whether this is the virtual Main aggregate account.
     */
    public function isMainAccount(): bool
    {
        return strtolower($this->name) === 'main';
    }
}
