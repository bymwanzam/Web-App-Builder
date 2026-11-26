<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $subscription;

    /**
     * Create a new notification instance.
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
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

        $template = EmailTemplate::findBySlug('subscription_cancelled');

        if ($template) {
            $parsed = $template->parse([
                'user_name' => $notifiable->name,
                'plan_name' => $plan ? $plan->name : 'N/A',
                'end_date' => $this->subscription->renewal_at ? $this->subscription->renewal_at->format('M j, Y') : 'N/A',
                'billing_url' => route('user.billing'),
                'app_name' => config('app.name'),
            ]);

            return (new MailMessage)
                ->subject($parsed['subject'])
                ->view('emails.template', ['content' => $parsed['body']]);
        }

        // Fallback to blade template
        return (new MailMessage)
            ->subject('Your '.($plan ? $plan->name : 'subscription').' subscription has been cancelled')
            ->view('emails.notifications.subscription-cancelled', [
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
            'plan_id' => $this->subscription->plan_id,
            'status' => 'cancelled',
        ];
    }
}
