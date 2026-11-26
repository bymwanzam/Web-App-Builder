<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $subscription;

    public $amount;

    public $currency;

    /**
     * Create a new notification instance.
     */
    public function __construct(Subscription $subscription, ?float $amount = null, ?string $currency = null)
    {
        $this->subscription = $subscription;
        $this->amount = $amount ?? ($subscription->plan ? $subscription->plan->price : 0);
        $this->currency = $currency ?? 'USD';
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
        $plan = $this->subscription->plan;

        $template = EmailTemplate::findBySlug('payment_failed');

        if ($template) {
            $parsed = $template->parse([
                'user_name' => $notifiable->name,
                'plan_name' => $plan ? $plan->name : 'N/A',
                'amount' => number_format($this->amount, 2),
                'currency' => $this->currency,
                'billing_url' => route('user.billing'),
                'app_name' => config('app.name'),
            ]);

            return (new MailMessage)
                ->subject($parsed['subject'])
                ->view('emails.template', ['content' => $parsed['body']]);
        }

        // Fallback to blade template
        return (new MailMessage)
            ->subject('Payment failed for '.($plan ? $plan->name : 'your subscription'))
            ->view('emails.notifications.payment-failed', [
                'user' => $notifiable,
                'subscription' => $this->subscription,
                'appName' => config('app.name'),
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subscription_id' => $this->subscription->id,
            'amount' => $this->amount,
            'status' => 'failed',
        ];
    }
}
