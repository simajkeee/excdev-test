<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUserCommand extends Command
{
    protected $signature = 'user:create';
    protected $description = 'Creates a new user interactively.';

    public function handle()
    {
        $name = $this->ask('Enter user name');
        $email = $this->ask('Enter user email');
        $password = $this->secret('Enter user password');
        $passwordConfirm = $this->secret('Confirm user password');
        if ($password !== $passwordConfirm) {
            $this->error('Passwords do not match.');

            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->info('User created successfully!');

        return self::SUCCESS;
    }
}