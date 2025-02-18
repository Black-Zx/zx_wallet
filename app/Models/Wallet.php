<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'balance'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function deposit($amount)
    {
        DB::transaction(function () use ($amount) {
            // Lock the wallet row for updates to prevent race conditions
            $wallet = Wallet::where('id', $this->id)->lockForUpdate()->first();

            // Apply the deposit
            $wallet->balance += $amount;

            // Calculate 1% rebate
            $rebate = $amount * 0.01;
            $wallet->balance += $rebate;

            // Save the updated wallet balance
            $wallet->save();

            // Log the deposit transaction
            $wallet->transactions()->create([
                'type' => 'deposit',
                'amount' => $amount,
            ]);

            // Log the rebate transaction
            $wallet->transactions()->create([
                'type' => 'rebate',
                'amount' => $rebate,
            ]);

            // Dispatch rebate job asynchronously
            \App\Jobs\CalculateRebate::dispatch($this->id, $amount);
        });

    }

    public function withdraw($amount)
    {
        DB::transaction(function () use ($amount) {
            // Lock the wallet row for updates
            $wallet = Wallet::where('id', $this->id)->lockForUpdate()->first();

            if ($wallet->balance < $amount) {
                throw new \Exception("Insufficient balance.");
            }

            $wallet->balance -= $amount;
            $wallet->save();

            // Log the withdrawal transaction
            $wallet->transactions()->create([
                'type' => 'withdrawal',
                'amount' => $amount,
            ]);
        });

    }
    
}
