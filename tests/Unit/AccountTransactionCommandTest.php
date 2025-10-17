<?php

namespace Tests\Unit;


use App\Jobs\TransactionJob;
use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AccountTransactionCommandTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('randomAccountNumberProvider')]
    #[Test]
    public function handleThrowsExceptionWhenAccountNotFound(string $accountNumber): void
    {
        $this->artisan('account:transaction', [
            'account' => $accountNumber,
        ])
        ->expectsOutput('Account not found')
        ->assertExitCode(Command::FAILURE);
    }

    #[DataProvider('invalidTypeNumberProvider')]
    #[Test]
    public function handleThrowsExceptionWithInvalidTypeNumber($invalidValue): void
    {
        $accountNumber = '0000000001';
        Account::factory()
            ->withAccountNumber($accountNumber)
            ->create();

        $this->artisan('account:transaction', [
            'account' => $accountNumber,
        ])
        ->expectsQuestion('Enter transaction amount', $invalidValue)
        ->expectsOutput('Failed to create transaction: Invalid type of value')
        ->assertExitCode(Command::FAILURE);
    }

    #[DataProvider('zeroValuesProvider')]
    #[Test]
    public function handleThrowsExceptionWithZeroValues($zeroValue): void
    {
        $accountNumber = '0000000001';
        Account::factory()
            ->withAccountNumber($accountNumber)
            ->create();

        $this->artisan('account:transaction', [
            'account' => $accountNumber,
        ])
        ->expectsQuestion('Enter transaction amount', $zeroValue)
        ->expectsOutput('Failed to create transaction: Amount can\'t be zero')
        ->assertExitCode(Command::FAILURE);
    }

    #[Test]
    public function handleHappyPath()
    {
        Queue::fake();
        $amount = 20;
        $description = 'Test transaction';
        $accountNumber = '0000000001';
        $account = Account::factory()
            ->withAccountNumber($accountNumber)
            ->create();

        $this->artisan('account:transaction', [
            'account' => $accountNumber,
        ])
            ->expectsQuestion('Enter transaction amount', $amount)
            ->expectsQuestion('Enter transaction description', $description)
            ->assertExitCode(Command::SUCCESS);

        Queue::assertPushed(function (TransactionJob $job) use ($amount, $description, $account) {
            return $job->amount === (float)$amount && $job->description === $description && $job->accountId === $account->id;
        });
    }

    public static function zeroValuesProvider(): array
    {
        return [
            ['0000000000'],
            ['0.0022'],
            ['0,002'],
            ['0.00'],
            ['0,00'],
            [0,00],
            [0.00],
        ];
    }

    public static function randomAccountNumberProvider(): array
    {
        return [
            ['0000000001'],
            ['0000000002'],
            ['0000000003'],
        ];
    }

    public static function invalidTypeNumberProvider(): array
    {
        return [
            [null],
            [false],
            [true],
            ['afasf,112s'],
            ['0,afaqw'],
            ['0.afaqw'],
            ['fasf,0012'],
            ['aghr.022']
        ];
    }
}
