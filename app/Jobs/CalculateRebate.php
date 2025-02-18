<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Wallet;

class CalculateRebate implements ShouldQueue
{
    use Queueable;

    protected $walletId;
    protected $depositAmount;

    public function __construct($walletId, $depositAmount)
    {
        $this->walletId = $walletId;
        $this->depositAmount = $depositAmount;
    }

    public function handle()
    {
        $wallet = Wallet::findOrFail($this->walletId);
        $rebate = $this->depositAmount * 0.01;
        $wallet->balance += $rebate;
        $wallet->save();

        $wallet->transactions()->create([
            'type' => 'rebate',
            'amount' => $rebate,
        ]);
    }


}
