<?php

use App\Http\Controllers\InstallController;
use App\Http\Middleware\InstallationGuard;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Installer Routes - Only accessible when not installed
Route::prefix('install')->middleware('not-installed')->group(function () {
    Route::get('/', [InstallController::class, 'welcome'])->name('install');

    Route::middleware(InstallationGuard::class)->group(function () {
        Route::get('requirements', [InstallController::class, 'requirements'])->name('install.requirements');
        Route::get('permissions', [InstallController::class, 'permissions'])->name('install.permissions');
        Route::get('database', [InstallController::class, 'database'])->name('install.database');
        Route::get('admin', [InstallController::class, 'admin'])->name('install.admin');
        Route::get('completed', [InstallController::class, 'completed'])->name('install.completed');
    });
});

// All application routes - Only accessible when installed
Route::middleware('installed')->group(function () {
    Route::get('/', function () {
        if (! SystemSetting::get('landing_page_enabled', true)) {
            if (Auth::check()) {
                return redirect('/user/apps');
            }

            return redirect('/login');
        }

        return view('landing');
    });

    // Public build download route (uses signed URLs for security)
    Route::get('builds/{build}/download', [App\Http\Controllers\BuildController::class, 'publicDownload'])
        ->name('builds.download.public');

    // Data export download route (uses token for security)
    Route::get('data-export/download/{token}', [App\Http\Controllers\DataExportController::class, 'download'])
        ->name('data-export.download');

    // Hosted page route (served to app WebViews)
    Route::get('p/{app}/{slug}', [App\Http\Controllers\AppPageController::class, 'show'])
        ->name('hosted-page.show');

    // Redirect dashboard to user apps
    Route::redirect('dashboard', '/user/apps')
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    Route::view('profile', 'profile')
        ->middleware(['auth'])
        ->name('profile');

    // Legal Pages (publicly accessible)
    Route::view('privacy', 'legal.privacy')->name('legal.privacy');
    Route::view('cookies', 'legal.cookies')->name('legal.cookies');
    Route::view('terms', 'legal.terms')->name('legal.terms');

    // Account deletion cancellation (from email link)
    Route::get('account/cancel-deletion/{token}', [App\Http\Controllers\AccountDeletionController::class, 'cancel'])
        ->name('account.cancel-deletion');

    // User Section Routes
    Route::middleware(['auth', 'verified'])->prefix('user')->group(function () {
        Route::view('apps', 'user.apps')->name('user.apps');

        // App detail/settings page (platform-specific editor)
        Route::get('apps/{app}', [App\Http\Controllers\AppController::class, 'show'])->name('user.app');

        Route::view('notifications', 'user.notifications')->name('user.notifications');
        Route::view('billing', 'user.billing')->name('user.billing');
        Route::view('api-keys', 'user.api-keys')->name('user.api-keys');

        // AI Chat routes (rate limited to prevent abuse)
        Route::middleware(['throttle:ai-chat'])->group(function () {
            Route::get('ai', function () {
                $hasConversations = \App\Models\AiConversation::where('user_id', auth()->id())->main()->exists();

                return view('user.ai', [
                    'conversationId' => null,
                    'appId' => null,
                    'hasConversations' => $hasConversations,
                ]);
            })->name('user.ai');

            Route::get('ai/{conversation}', function (App\Models\AiConversation $conversation) {
                // Ensure user owns this conversation
                if ($conversation->user_id !== auth()->id()) {
                    abort(403);
                }

                return view('user.ai', [
                    'conversationId' => $conversation->id,
                    'appId' => $conversation->app_id,
                    'hasConversations' => true, // Always show sidebar when viewing conversation
                ]);
            })->missing(function () {
                // Redirect to base AI route if conversation not found (e.g., after deletion)
                return redirect()->route('user.ai');
            })->name('user.ai.conversation');

            // Streaming endpoint
            Route::post('ai/{conversation}/stream', [App\Http\Controllers\AiStreamController::class, 'stream'])
                ->name('user.ai.stream');
        });

        // Invoice routes
        Route::get('invoices/{transaction}/view', [App\Http\Controllers\InvoiceController::class, 'view'])->name('user.invoices.view');
    });

    // Admin Section Routes (matching prototype)
    Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
        Route::view('overview', 'admin.overview')->name('admin.overview');
        Route::view('users', 'admin.users')->name('admin.users');
        Route::view('subscriptions', 'admin.subscriptions')->name('admin.subscriptions');
        Route::view('transactions', 'admin.transactions')->name('admin.transactions');
        Route::view('plans', 'admin.plans')->name('admin.plans');
        Route::view('plugins', 'admin.plugins')->name('admin.plugins');
        Route::view('languages', 'admin.languages')->name('admin.languages');
        Route::view('builders', 'admin.builders')->name('admin.builders');
        Route::view('cronjobs', 'admin.cronjobs')->name('admin.cronjobs');
        Route::view('email-templates', 'admin.email-templates')->name('admin.email-templates');
        Route::view('audit-logs', 'admin.audit-logs')->name('admin.audit-logs');
        Route::view('settings', 'admin.settings')->name('admin.settings');
    });

    // Payment Gateway Routes
    Route::prefix('payment-gateways')->name('payment-gateways.')->group(function () {
        Route::post('{plugin}/webhook', [App\Http\Controllers\PaymentGatewayController::class, 'webhook'])
            ->name('webhook');
        Route::get('{plugin}/webhook', [App\Http\Controllers\PaymentGatewayController::class, 'webhookInfo'])
            ->name('webhook.info');
        Route::get('callback', [App\Http\Controllers\PaymentGatewayController::class, 'callback'])
            ->name('callback');
    });

    // Plugin Icon Route
    Route::get('plugins/{slug}/{filename}', [App\Http\Controllers\PluginIconController::class, 'show'])
        ->name('plugin.icon');

    // Redirect /docs to /docs/api
    Route::redirect('docs', 'docs/api');

    // Note: API Documentation at /docs/api is auto-registered by Scribe

    require __DIR__.'/auth.php';
});
