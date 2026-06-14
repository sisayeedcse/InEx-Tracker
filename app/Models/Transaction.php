<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    protected $fillable = [
        'account_id',
        'type',
        'amount',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * The account this transaction belongs to.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /* ---- Query Scopes ---- */

    public function scopeIncome(Builder $query): Builder
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense(Builder $query): Builder
    {
        return $query->where('type', 'expense');
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ]);
    }

    public function scopeDateRange(Builder $query, ?string $start, ?string $end): Builder
    {
        if ($start) {
            $query->whereDate('created_at', '>=', $start);
        }
        if ($end) {
            $query->whereDate('created_at', '<=', $end);
        }
        return $query;
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if ($keyword) {
            $query->where('note', 'like', '%' . $keyword . '%');
        }
        return $query;
    }

    /**
     * Get transactions grouped by month for the last N months.
     * Returns array: [ ['month' => 'Jan 25', 'income' => x, 'expense' => y], ... ]
     */
    public static function monthlyTotals(int $months = 6): array
    {
        $result = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $label = $date->format('M y');

            $income  = self::income()
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');

            $expense = self::expense()
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');

            $result[] = [
                'month'   => $label,
                'income'  => round((float) $income, 2),
                'expense' => round((float) $expense, 2),
            ];
        }

        return $result;
    }
}
