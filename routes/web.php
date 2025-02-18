<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::post('wallets/{walletId}/deposit', [WalletController::class, 'deposit']);
// Route::post('wallets/{walletId}/withdraw', [WalletController::class, 'withdraw']);
// Route::get('wallets/{walletId}/balance', [WalletController::class, 'balance']);
// Route::get('wallets/{walletId}/transactions', [WalletController::class, 'transactionHistory']);
