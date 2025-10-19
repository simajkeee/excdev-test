<?php

namespace App\Console\Commands;

use App\Jobs\TransactionJob;
use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
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
            if (is_string($amount) && str_contains($amount, ',')) {
                $amount = str_replace(',', '.', $amount);
            }
            if (!is_numeric($amount)) {
                throw new InvalidArgumentException('Invalid type of value');
            }
            $amount = round((float)$amount, 2);
            if ($amount == 0.0) {
                throw new InvalidArgumentException('Amount can\'t be zero');
            }

            $description = $this->ask('Enter transaction description');

            TransactionJob::dispatch($amount, $account->id, $description ?? '');

            return self::SUCCESS;
        } catch (ModelNotFoundException $e) {
            $msg = 'Account not found';
            $this->error($msg);
            Log::error($msg);

            return self::FAILURE;
        } catch (Throwable $e) {
            $msg = 'Failed to create transaction: ' . $e->getMessage();
            $this->error($msg);
            Log::error($msg);

            return self::FAILURE;
        }
    }
}
