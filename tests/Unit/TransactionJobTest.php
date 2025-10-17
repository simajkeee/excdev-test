<?php

namespace Tests\Unit;

use App\Jobs\TransactionJob;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransactionJobTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function handleTrowExceptionAccountNotFound(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $job = new TransactionJob(1, 1, 'test');
        $job->handle();
    }

    #[DataProvider('wrongAmountValuesProvider')]
    #[Test]
    public function handleTrowExceptionTransactionAmountIsZero($amount): void
    {
        $account = Account::factory()->create();
        $this->expectException(InvalidArgumentException::class);

        $job = new TransactionJob((float)$amount, $account->id, 'test');
        $job->handle();
    }

    #[DataProvider('wrongAmountValuesProvider')]
    #[Test]
    public function handleAccountBalanceSameWhenTransactionAmountIsZero($amount): void
    {
        $initialBalance = 200.00;
        $account = Account::factory()->withBalance($initialBalance)->create();
        $this->expectException(InvalidArgumentException::class);

        $job = new TransactionJob((float)$amount, $account->id, 'test');
        $job->handle();
        $account->refresh();

        $this->assertEquals($initialBalance, $account->balance);
    }

    #[DataProvider('deductAccountBalanceProvider')]
    #[Test]
    public function handleAccountBalanceWithNegativeAmountTransaction(float $balance, float $trxAmount, float $expected): void
    {
        $account = Account::factory()->withBalance($balance)->create();
        $job = new TransactionJob($trxAmount, $account->id, 'test');
        $job->handle();
        $account->refresh();

        $this->assertEquals($expected, $account->balance);
    }

    #[DataProvider('addAccountBalanceProvider')]
    #[Test]
    public function handleAccountBalanceWithAmountAboveZeroTransaction(float $balance, float $trxAmount, float $expected): void
    {
        $account = Account::factory()->withBalance($balance)->create();
        $job = new TransactionJob($trxAmount, $account->id, 'test');
        $job->handle();
        $account->refresh();

        $this->assertEquals($expected, $account->balance);
    }

    #[DataProvider('negativeValuesProvider')]
    #[Test]
    public function handleAccountBalanceThrowExceptionNegativeBalance(float $negativeTrxAmount): void
    {
        $account = Account::factory()->create();
        $this->expectException(InvalidArgumentException::class);
        $job = new TransactionJob($negativeTrxAmount, $account->id, 'test');
        $job->handle();
    }

    #[Test]
    public function handleHappyPath(): void
    {
        $account = Account::factory()->create();
        $description = 'test';
        $job = new TransactionJob(200, $account->id, $description);
        $job->handle();
        $account->refresh();

        $this->assertDatabaseCount('transactions', 1);
        $trx = Transaction::firstOrFail();
        $this->assertEquals(200, $account->balance);
        $this->assertEquals($description, $trx->description);
    }

    #[Test]
    public function handleTransactionIsNotCreatedWhenValueIsZero(): void
    {
        $account = Account::factory()->create();
        $this->expectException(InvalidArgumentException::class);
        $job = new TransactionJob(0, $account->id, 'test');
        $job->handle();

        $this->assertDatabaseCount('transactions', 0);
    }

    #[Test]
    public function handleTransactionIsNotCreatedWhenAccountValueLessThenZero(): void
    {
        $account = Account::factory()->create();
        $this->expectException(InvalidArgumentException::class);
        $job = new TransactionJob(-200.00, $account->id, 'test');
        $job->handle();

        $this->assertDatabaseCount('transactions', 0);
    }

    public static function negativeValuesProvider(): array
    {
        return [
            [-0.01],
            [-200],
            [-0.1],
        ];
    }

    public static function wrongAmountValuesProvider(): array
    {
        return [
            ['0.0012'],
            ['0,0012'],
            [0,0012],
            [0.0012],
            [0.00],
            [null],
            [false],
            ['0'],
            ['dasfa'],
            ['0,fafas'],
            ['0,dfafas'],
            ['dfafas'],
            ['dfafas,af1rr1'],
            ['dfaad12fas.af1rr1'],
        ];
    }

    public static function addAccountBalanceProvider(): array
    {
        return [
            [1, 1, 2],
            [1, 0.05, 1.05],
            [0.05, 0.02, 0.07],
        ];
    }

    public static function deductAccountBalanceProvider(): array
    {
        return [
            [1, -1, 0],
            [1, -0.05, 0.95],
            [0.05, -0.02, 0.03],
        ];
    }
}
