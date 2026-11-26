<?php

namespace App\Http\Controllers;

use App\Services\PluginManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PaymentGatewayController extends Controller
{
    public function __construct(private PluginManager $pluginManager) {}

    /**
     * Handle webhook from payment gateway.
     */
    public function webhook(Request $request, string $plugin): Response
    {
        try {
            // Get the plugin instance
            $pluginInstance = $this->pluginManager->getGateway(ucfirst($plugin));

            if (! $pluginInstance) {
                Log::error('Payment gateway plugin not found', ['plugin' => $plugin]);

                return response()->json(['error' => 'Plugin not found'], 404);
            }

            // Handle the webhook
            return $pluginInstance->handleWebhook($request);
        } catch (\Exception $e) {
            Log::error('Payment gateway webhook failed', [
                'plugin' => $plugin,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Show webhook information (for GET requests).
     */
    public function webhookInfo(string $plugin)
    {
        return response()->json([
            'message' => 'Webhook endpoint is active',
            'plugin' => ucfirst($plugin),
            'method' => 'POST',
            'description' => 'This endpoint receives webhook notifications from '.ucfirst($plugin).'. It only accepts POST requests.',
        ], 200);
    }

    /**
     * Handle callback from payment gateway after user completes payment.
     */
    public function callback(Request $request)
    {
        $plugin = $request->query('plugin');

        if (! $plugin) {
            return redirect()->route('user.billing')
                ->with('toast', ['type' => 'error', 'message' => 'Invalid payment callback']);
        }

        try {
            // Get the plugin instance
            $pluginInstance = $this->pluginManager->getGateway(ucfirst($plugin));

            if (! $pluginInstance) {
                return redirect()->route('user.billing')
                    ->with('toast', ['type' => 'error', 'message' => 'Payment gateway not found']);
            }

            // Handle the callback
            return $pluginInstance->callback($request);
        } catch (\Exception $e) {
            Log::error('Payment gateway callback failed', [
                'plugin' => $plugin,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('user.billing')
                ->with('toast', ['type' => 'error', 'message' => 'Payment processing failed']);
        }
    }
}
