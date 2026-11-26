<?php

namespace App\Notifications;

use App\Models\Subscription;
use App\Models\SystemSetting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class AdminPaymentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $eventType;

    public ?User $user;

    public ?Subscription $subscription;

    public ?Transaction $transaction;

    public array $extraData;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        string $eventType,
        ?User $user = null,
        ?Subscription $subscription = null,
        ?Transaction $transaction = null,
        array $extraData = []
    ) {
        $this->eventType = $eventType;
        $this->user = $user;
        $this->subscription = $subscription;
        $this->transaction = $transaction;
        $this->extraData = $extraData;
    }

    /**
     * Send admin notification if event is enabled and email is configured.
     */
    public static function sendIfEnabled(
        string $eventType,
        ?User $user = null,
        ?Subscription $subscription = null,
        ?Transaction $transaction = null,
        array $extraData = []
    ): void {
        $adminEmail = SystemSetting::get('admin_notification_email');
        $enabledEvents = SystemSetting::get('admin_notification_events', []);

        if (! $adminEmail || ! in_array($eventType, $enabledEvents)) {
            return;
        }

        try {
            NotificationFacade::route('mail', $adminEmail)
                ->notify(new self($eventType, $user, $subscription, $transaction, $extraData));
        } catch (\Exception $e) {
            \Log::error('Failed to send AdminPaymentNotification', [
                'event_type' => $eventType,
                'admin_email' => $adminEmail,
                'user_id' => $user?->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailData = match ($this->eventType) {
            'subscription_activated' => $this->subscriptionActivatedData(),
            'payment_completed' => $this->paymentCompletedData(),
            'payment_failed' => $this->paymentFailedData(),
            'subscription_cancelled' => $this->subscriptionCancelledData(),
            'subscription_expired' => $this->subscriptionExpiredData(),
            'user_registered' => $this->userRegisteredData(),
            'build_completed' => $this->buildCompletedData(),
            'build_failed' => $this->buildFailedData(),
            default => $this->defaultData(),
        };

        return (new MailMessage)
            ->subject($mailData['subject'])
            ->view('emails.notifications.admin', array_merge($mailData, [
                'appName' => config('app.name'),
            ]));
    }

    protected function subscriptionActivatedData(): array
    {
        $plan = $this->subscription?->plan;

        return [
            'subject' => 'New Subscription Activated - '.($this->user?->name ?? 'Unknown'),
            'title' => 'New Subscription Activated',
            'introLine' => 'A new subscription has been activated.',
            'user' => $this->user,
            'detailsBox' => [
                'Plan' => $plan?->name ?? 'N/A',
                'Amount' => ($plan?->currency ?? 'USD').' '.($plan ? number_format($plan->price, 2) : '0.00'),
                'Next Billing' => $this->subscription?->renewal_at ? $this->subscription->renewal_at->format('M j, Y') : 'N/A',
            ],
            'boxClass' => 'success-box',
            'actionUrl' => route('admin.subscriptions'),
            'actionText' => 'View Subscriptions',
        ];
    }

    protected function paymentCompletedData(): array
    {
        $subscription = $this->transaction?->subscription ?? $this->subscription;
        $plan = $subscription?->plan;

        return [
            'subject' => 'Payment Received - '.($this->user?->name ?? 'Unknown'),
            'title' => 'Payment Received',
            'introLine' => 'A payment has been received.',
            'user' => $this->user,
            'detailsBox' => [
                'Amount' => ($this->transaction?->currency ?? 'USD').' '.($this->transaction ? number_format($this->transaction->amount, 2) : '0.00'),
                'Transaction ID' => $this->transaction?->transaction_id ?? $this->transaction?->id ?? 'N/A',
                'Plan' => $plan?->name ?? 'N/A',
            ],
            'boxClass' => 'success-box',
            'actionUrl' => route('admin.transactions'),
            'actionText' => 'View Transactions',
        ];
    }

    protected function paymentFailedData(): array
    {
        $plan = $this->subscription?->plan;
        $amount = $this->extraData['amount'] ?? ($plan?->price ?? 0);

        return [
            'subject' => 'Payment Failed - '.($this->user?->name ?? 'Unknown'),
            'title' => 'Payment Failed',
            'introLine' => 'A payment has failed.',
            'user' => $this->user,
            'detailsBox' => [
                'Amount' => ($this->extraData['currency'] ?? 'USD').' '.number_format($amount, 2),
                'Plan' => $plan?->name ?? 'N/A',
            ],
            'boxClass' => 'error-box',
            'actionUrl' => route('admin.subscriptions'),
            'actionText' => 'View Subscriptions',
        ];
    }

    protected function subscriptionCancelledData(): array
    {
        $plan = $this->subscription?->plan;

        return [
            'subject' => 'Subscription Cancelled - '.($this->user?->name ?? 'Unknown'),
            'title' => 'Subscription Cancelled',
            'introLine' => 'A subscription has been cancelled.',
            'user' => $this->user,
            'detailsBox' => [
                'Plan' => $plan?->name ?? 'N/A',
                'End Date' => $this->subscription?->ends_at ? $this->subscription->ends_at->format('M j, Y') : 'N/A',
            ],
            'boxClass' => 'warning-box',
            'actionUrl' => route('admin.subscriptions'),
            'actionText' => 'View Subscriptions',
        ];
    }

    protected function subscriptionExpiredData(): array
    {
        $plan = $this->subscription?->plan;

        return [
            'subject' => 'Subscription Expired - '.($this->user?->name ?? 'Unknown'),
            'title' => 'Subscription Expired',
            'introLine' => 'A subscription has expired.',
            'user' => $this->user,
            'detailsBox' => [
                'Plan' => $plan?->name ?? 'N/A',
            ],
            'boxClass' => 'warning-box',
            'actionUrl' => route('admin.subscriptions'),
            'actionText' => 'View Subscriptions',
        ];
    }

    protected function userRegisteredData(): array
    {
        return [
            'subject' => 'New User Registration - '.($this->user?->name ?? 'Unknown'),
            'title' => 'New User Registration',
            'introLine' => 'A new user has registered on your platform.',
            'user' => $this->user,
            'detailsBox' => [
                'Registered At' => now()->format('M j, Y g:i A'),
            ],
            'boxClass' => 'info-box',
            'actionUrl' => route('admin.users'),
            'actionText' => 'View Users',
        ];
    }

    protected function buildCompletedData(): array
    {
        $appName = $this->extraData['app_name'] ?? 'Unknown App';
        $platform = ucfirst($this->extraData['platform'] ?? 'unknown');
        $version = $this->extraData['version'] ?? 'N/A';

        return [
            'subject' => 'Build Completed - '.$appName,
            'title' => 'Build Completed',
            'introLine' => 'A build has been completed successfully.',
            'user' => $this->user,
            'detailsBox' => [
                'App' => $appName,
                'Platform' => $platform,
                'Version' => $version,
            ],
            'boxClass' => 'success-box',
            'actionUrl' => route('admin.overview'),
            'actionText' => 'View Dashboard',
        ];
    }

    protected function buildFailedData(): array
    {
        $appName = $this->extraData['app_name'] ?? 'Unknown App';
        $platform = ucfirst($this->extraData['platform'] ?? 'unknown');
        $error = $this->extraData['error'] ?? 'Unknown error';

        return [
            'subject' => 'Build Failed - '.$appName,
            'title' => 'Build Failed',
            'introLine' => 'A build has failed.',
            'user' => $this->user,
            'detailsBox' => [
                'App' => $appName,
                'Platform' => $platform,
                'Error' => $error,
            ],
            'boxClass' => 'error-box',
            'actionUrl' => route('admin.overview'),
            'actionText' => 'View Dashboard',
        ];
    }

    protected function defaultData(): array
    {
        return [
            'subject' => 'Admin Notification - '.$this->eventType,
            'title' => 'Admin Notification',
            'introLine' => 'An event has occurred: '.$this->eventType,
            'user' => $this->user,
            'boxClass' => 'info-box',
            'actionUrl' => route('admin.overview'),
            'actionText' => 'View Dashboard',
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_type' => $this->eventType,
            'user_id' => $this->user?->id,
            'subscription_id' => $this->subscription?->id,
            'transaction_id' => $this->transaction?->id,
        ];
    }
}
