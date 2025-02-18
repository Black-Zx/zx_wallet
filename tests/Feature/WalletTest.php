<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Support\Facades\Queue;
use App\Models\Transaction;

class WalletTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    use RefreshDatabase;

    public function test_deposit_with_rebate()
    {
        Queue::fake();

        // Create a user instance using the factory
        $user = User::factory()->create();
        $wallet = Wallet::create(['user_id' => $user->id, 'balance' => 0]);

        $response = $this->postJson("/api/wallets/{$wallet->id}/deposit", ['amount' => 100]);

        $response->assertStatus(200);
        // $wallet->refresh()->balance insteads of $wallet->refresh();

        // Assert balance after deposit and rebate
        $this->assertEquals(101.00, $wallet->refresh()->balance);

        // Ensure rebate was recorded
        $this->assertCount(2, Transaction::all());
        $this->assertEquals(1, Transaction::where('type', 'rebate')->count());
    }

    public function test_concurrent_deposits()
    {
        // Fake the queue to simulate immediate execution of jobs
        Queue::fake();

        $user = User::factory()->create();
        $wallet = Wallet::create(['user_id' => $user->id, 'balance' => 0]);

        // Simulate concurrent deposits
        $this->postJson("/api/wallets/{$wallet->id}/deposit", ['amount' => 100]);
        $this->postJson("/api/wallets/{$wallet->id}/deposit", ['amount' => 200]);

        $wallet->refresh();

        // Expected balance after two deposits with rebates
        $expectedBalance = 100 + (100 * 0.01) + 200 + (200 * 0.01); // 100 + 1 + 200 + 2 = 303

        $this->assertEquals(303.00, $wallet->balance);

        // Check that both rebate transactions were added
        $this->assertCount(4, Transaction::all()); // 2 deposits + 1 rebate for each deposit
        $this->assertEquals(2, Transaction::where('type', 'rebate')->count());
    }

}
