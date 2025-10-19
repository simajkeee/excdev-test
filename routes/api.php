<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// http://localhost:8000/api/users/1/transactions
Route::get('/users/{user}/transactions', [TransactionController::class, 'index'])
    ->name('transactions.index');