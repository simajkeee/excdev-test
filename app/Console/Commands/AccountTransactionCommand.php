<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Throwable;

class AccountTransactionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account:transaction {account}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $account = Account::where('account_number', $this->argument('account'))
                ->firstOrFail();

            $amount = $this->ask('Enter transaction amount');
            $amount = str_replace(',', '.', $amount);
            if (!is_numeric($amount) || $amount == 0.0) {
                $this->error('Invalid type of value');

                return self::FAILURE;
            };

            $description = $this->ask('Enter transaction description');

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

            $this->info("Created transaction #{$transaction->id} with amount {$transaction->amount}");

            return self::SUCCESS;
        } catch (ModelNotFoundException $e) {
            $this->error('Account not found');

            return self::FAILURE;
        } catch (Throwable $e) {
            $this->error('Failed to create transaction: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
