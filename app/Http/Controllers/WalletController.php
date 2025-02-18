<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;

use App\Models\Transaction;
use App\Jobs\CalculateRebate;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function deposit(Request $request, $walletId)
    {
        $wallet = Wallet::findOrFail($walletId);

        $amount = $request->input('amount');
        if ($amount <= 0) {
            return response()->json(['message' => 'Deposit amount must be greater than zero.'], 400);
        }

        try {
            $wallet->deposit($amount);
            return response()->json(['message' => 'Deposit successful!', 'balance' => $wallet->balance]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function withdraw(Request $request, $walletId)
    {
        $wallet = Wallet::findOrFail($walletId);

        $amount = $request->input('amount');
        if ($amount <= 0) {
            return response()->json(['message' => 'Withdrawal amount must be greater than zero.'], 400);
        }

        try {
            $wallet->withdraw($amount);
            return response()->json(['message' => 'Withdrawal successful!', 'balance' => $wallet->balance]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function balance($walletId)
    {
        $wallet = Wallet::findOrFail($walletId);
        return response()->json(['balance' => $wallet->balance]);
    }

    public function transactionHistory($walletId)
    {
        $wallet = Wallet::findOrFail($walletId);
        $transactions = $wallet->transactions()->latest()->get();
        return response()->json(['transactions' => $transactions]);
    }

}
