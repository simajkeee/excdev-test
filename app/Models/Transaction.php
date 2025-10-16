<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'account_id',
        'description',
        'amount',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function setAmountAttribute($value): void
    {
        $amount = (float) $value;
        if (empty($value) || $amount == 0.0) {
            throw new \InvalidArgumentException("Transaction amount cannot be empty or zero");
        }

        $this->attributes['amount'] = $value;
    }

}
