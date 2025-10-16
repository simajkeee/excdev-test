<?php

namespace App\Console\Commands;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUserCommand extends Command
{
    protected $signature = 'user:create {--admin}';
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

        $role = $this->option('admin') ? Roles::ADMIN : Roles::USER;
        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
        ]);

        $this->info('User created successfully!');

        return self::SUCCESS;
    }
}