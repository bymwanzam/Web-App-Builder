<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get plans
        $freePlan = Plan::where('name', 'Free')->first();
        $proPlan = Plan::where('name', 'Pro')->first();

        // Create admin user (ID = 1)
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@appy.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Create Pro plan subscription for admin - only in local environment
        if (app()->environment('local') && $proPlan) {
            // Set plan_id and build_credits on user
            $admin->update([
                'plan_id' => $proPlan->id,
                'build_credits' => $proPlan->monthly_build_credits,
            ]);

            // Create subscription
            Subscription::create([
                'user_id' => $admin->id,
                'plan_id' => $proPlan->id,
                'status' => 'active',
                'amount' => $proPlan->price,
                'renewal_at' => now()->addMonth(),
            ]);
        }

        // Create regular user (ID = 2) - only in local environment
        if (app()->environment('local')) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'user@appy.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'user',
                'status' => 'active',
            ]);

            // Create Free plan subscription for regular user
            if ($freePlan) {
                // Set plan_id and build_credits on user
                $user->update([
                    'plan_id' => $freePlan->id,
                    'build_credits' => $freePlan->monthly_build_credits,
                ]);

                // Create subscription
                Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $freePlan->id,
                    'status' => 'active',
                    'amount' => $freePlan->price,
                    'renewal_at' => now()->addMonth(),
                ]);
            }

            // Create 20 mock users for testing using Faker
            $faker = \Faker\Factory::create();

            for ($i = 0; $i < 20; $i++) {
                User::create([
                    'name' => $faker->name(),
                    'email' => $faker->unique()->safeEmail(),
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'role' => 'user',
                    'status' => 'active',
                ]);
            }
        }
    }
}
