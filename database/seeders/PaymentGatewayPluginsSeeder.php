<?php

namespace Database\Seeders;

use App\Models\Plugin;
use Illuminate\Database\Seeder;

class PaymentGatewayPluginsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Production payment gateways (always installed)
        $plugins = [
            [
                'name' => 'Bank Transfer',
                'slug' => 'bank-transfer',
                'type' => 'payment_gateway',
                'class' => 'App\Plugins\PaymentGateways\BankTransferPlugin',
                'version' => '1.0.0',
                'status' => 'inactive',
                'config' => null,
                'migrations' => [],
                'metadata' => json_decode(file_get_contents(app_path('Plugins/PaymentGateways/bank-transfer/plugin.json')), true),
                'installed_at' => now(),
            ],
            [
                'name' => 'PayPal',
                'slug' => 'paypal',
                'type' => 'payment_gateway',
                'class' => 'App\Plugins\PaymentGateways\PayPalPlugin',
                'version' => '1.0.0',
                'status' => 'inactive',
                'config' => null,
                'migrations' => [],
                'metadata' => json_decode(file_get_contents(app_path('Plugins/PaymentGateways/paypal/plugin.json')), true),
                'installed_at' => now(),
            ],
        ];

        // Add development payment gateways in local environment
        if (app()->environment('local')) {
            $developmentPlugins = [
                [
                    'name' => 'Stripe',
                    'slug' => 'stripe',
                    'type' => 'payment_gateway',
                    'class' => 'App\Plugins\PaymentGateways\StripePlugin',
                    'version' => '1.0.0',
                    'status' => 'inactive',
                    'config' => null,
                    'migrations' => [],
                    'metadata' => json_decode(file_get_contents(app_path('Plugins/PaymentGateways/stripe/plugin.json')), true),
                    'installed_at' => now(),
                ],
                [
                    'name' => 'Paystack',
                    'slug' => 'paystack',
                    'type' => 'payment_gateway',
                    'class' => 'App\Plugins\PaymentGateways\PaystackPlugin',
                    'version' => '1.0.0',
                    'status' => 'inactive',
                    'config' => null,
                    'migrations' => [],
                    'metadata' => json_decode(file_get_contents(app_path('Plugins/PaymentGateways/paystack/plugin.json')), true),
                    'installed_at' => now(),
                ],
                [
                    'name' => 'Razorpay',
                    'slug' => 'razorpay',
                    'type' => 'payment_gateway',
                    'class' => 'App\Plugins\PaymentGateways\RazorpayPlugin',
                    'version' => '1.0.0',
                    'status' => 'inactive',
                    'config' => null,
                    'migrations' => [],
                    'metadata' => json_decode(file_get_contents(app_path('Plugins/PaymentGateways/razorpay/plugin.json')), true),
                    'installed_at' => now(),
                ],
                [
                    'name' => 'Crypto.com Pay',
                    'slug' => 'cryptocom',
                    'type' => 'payment_gateway',
                    'class' => 'App\Plugins\PaymentGateways\CryptoComPlugin',
                    'version' => '1.0.0',
                    'status' => 'inactive',
                    'config' => null,
                    'migrations' => [],
                    'metadata' => json_decode(file_get_contents(app_path('Plugins/PaymentGateways/cryptocom/plugin.json')), true),
                    'installed_at' => now(),
                ],
            ];

            $plugins = array_merge($plugins, $developmentPlugins);
        }

        // Install plugins
        foreach ($plugins as $pluginData) {
            Plugin::updateOrCreate(
                ['slug' => $pluginData['slug']],
                $pluginData
            );

            $this->command->info("Installed: {$pluginData['name']}");
        }

        $this->command->info('Total plugins: '.Plugin::count());
    }
}
