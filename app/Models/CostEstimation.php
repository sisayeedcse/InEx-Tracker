<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostEstimation extends Model
{
    protected $fillable = [
        'account_id',
        'amount',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the account that owns the cost estimation.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
