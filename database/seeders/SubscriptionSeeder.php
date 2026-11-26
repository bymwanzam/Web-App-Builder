<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only seed mock subscriptions in local environment
        if (! app()->environment('local')) {
            return;
        }

        $users = User::get();
        $plans = Plan::all();

        if ($users->count() === 0 || $plans->count() === 0) {
            return;
        }

        $freePlan = $plans->where('name', 'Free')->first();
        $proPlan = $plans->where('name', 'Pro')->first();
        $enterprisePlan = $plans->where('name', 'Enterprise')->first();

        $faker = \Faker\Factory::create();
        $statuses = ['active', 'pending', 'expired', 'canceled'];

        // Skip admin (id=1) and test user (id=2) - they already have subscriptions from UserSeeder
        $mockUsers = $users->where('id', '>', 2);

        foreach ($mockUsers as $user) {
            // Randomly assign a plan (weighted: 60% Free, 30% Pro, 10% Enterprise)
            $random = rand(1, 100);
            if ($random <= 60) {
                $plan = $freePlan;
            } elseif ($random <= 90) {
                $plan = $proPlan;
            } else {
                $plan = $enterprisePlan;
            }

            // Randomly assign status (weighted: 80% active, 10% pending, 5% expired, 5% canceled)
            $random = rand(1, 100);
            if ($random <= 80) {
                $status = 'active';
            } elseif ($random <= 90) {
                $status = 'pending';
            } elseif ($random <= 95) {
                $status = 'expired';
            } else {
                $status = 'canceled';
            }

            // Set plan_id and build_credits on user
            $user->update([
                'plan_id' => $plan->id,
                'build_credits' => $plan->monthly_build_credits,
            ]);

            // Create subscription
            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => $status,
                'amount' => $plan->price,
                'renewal_at' => $faker->dateTimeBetween('now', '+1 year'),
            ]);
        }
    }
}
