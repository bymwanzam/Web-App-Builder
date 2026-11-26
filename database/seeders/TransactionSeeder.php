<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only seed mock transactions in local environment
        if (! app()->environment('local')) {
            return;
        }

        $subscriptions = \App\Models\Subscription::with('user', 'plan')->get();

        if ($subscriptions->count() === 0) {
            return;
        }

        $faker = \Faker\Factory::create();
        $paymentGateways = ['bank-transfer', 'paypal'];
        $statuses = ['completed', 'pending', 'failed'];

        $transactionCounter = 1000;

        foreach ($subscriptions as $subscription) {
            // Skip Free plan subscriptions (amount = 0)
            if ($subscription->plan->price == 0) {
                continue;
            }

            // Create 1-3 transactions per paid subscription
            $transactionCount = rand(1, 3);

            for ($i = 0; $i < $transactionCount; $i++) {
                // Randomly assign status (weighted: 85% completed, 10% pending, 5% failed)
                $random = rand(1, 100);
                if ($random <= 85) {
                    $status = 'completed';
                } elseif ($random <= 95) {
                    $status = 'pending';
                } else {
                    $status = 'failed';
                }

                \App\Models\Transaction::create([
                    'transaction_id' => 'TRX-'.str_pad($transactionCounter++, 4, '0', STR_PAD_LEFT),
                    'user_id' => $subscription->user_id,
                    'subscription_id' => $subscription->id,
                    'amount' => $subscription->amount,
                    'status' => $status,
                    'type' => 'subscription',
                    'payment_method' => $paymentGateways[array_rand($paymentGateways)],
                    'transaction_date' => $faker->dateTimeBetween('-6 months', 'now'),
                ]);
            }
        }
    }
}
