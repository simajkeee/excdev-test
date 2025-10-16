<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
        'account_number',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function setBalanceAttribute($value): void
    {
        $value = (float) $value;
        if ($value < 0) {
            throw new \InvalidArgumentException("Balance cannot be negative");
        }

        $this->attributes['balance'] = $value;
    }
}
