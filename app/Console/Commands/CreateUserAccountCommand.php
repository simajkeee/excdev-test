<?php

namespace App\Console\Commands;

use App\Enums\Roles;
use App\Models\Account;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreateUserAccountCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-account {user-email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $email = $this->argument('user-email');
            $userId = User::where('email', $email)
                ->where('role', Roles::ACCOUNT_OWNER)
                ->value('id');

            if (!$userId) {
                throw new ModelNotFoundException;
            }

            $last = Account::orderByDesc('id')->first();
            $accLength = 10;
            if ($last) {
                $lastAccountNumber = $last->account_number;
            } else {
                $lastAccountNumber = str_repeat('0', $accLength);
            }

            $num = (int) $lastAccountNumber;
            $num++;
            $newAccountNumber = str_pad(
                (string) $num,
                $accLength,
                '0',
                STR_PAD_LEFT,
            );

            $account = Account::create([
                'user_id' => $userId,
                'balance' => 0.00,
                'account_number' => $newAccountNumber,
            ]);

            $this->info("Created account #{$account->id} with number {$account->account_number}");

            return self::SUCCESS;
        } catch (ModelNotFoundException $e) {
            $this->error('User not found.');

            return self::FAILURE;
        } catch (\Throwable $e) {
            $this->error('Failed to create account: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
