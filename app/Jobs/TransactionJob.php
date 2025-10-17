<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class TransactionJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public float $amount,
        public int $accountId,
        public string $description = '',
    ) { }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info(
            sprintf(
                'handling TransactionJob: %.2f, %d, %s',
                $this->amount,
                $this->accountId,
                $this->description,
            )
        );

        $amount = round($this->amount, 2);
        if ($amount == 0.0) {
            throw new InvalidArgumentException('Amount can\'t be zero');
        }
        $account = Account::findOrFail($this->accountId);
        $description = $this->description;
        $transaction = DB::transaction(function () use ($account, $amount, $description) {
            $newBalance = $account->balance + $amount;
            $account->balance = $newBalance;
            $account->save();

            return Transaction::create([
                'account_id' => $account->id,
                'amount' => $amount,
                'description' => $description,
            ]);
        });

        Log::info("Created transaction #{$transaction->id} with amount {$transaction->amount}");
    }
}
