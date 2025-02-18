<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WalletController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Grouping routes under 'api' middleware (optional) for better structure
Route::middleware('api')->group(function () {
    
    // Route for depositing funds into a wallet
    Route::post('wallets/{walletId}/deposit', [WalletController::class, 'deposit']);
    
    // Route for withdrawing funds from a wallet
    Route::post('wallets/{walletId}/withdraw', [WalletController::class, 'withdraw']);
    
    // Route to get the current balance of a wallet
    Route::get('wallets/{walletId}/balance', [WalletController::class, 'balance']);
    
    // Route to get the transaction history of a wallet
    Route::get('wallets/{walletId}/transactions', [WalletController::class, 'transactionHistory']);
});
