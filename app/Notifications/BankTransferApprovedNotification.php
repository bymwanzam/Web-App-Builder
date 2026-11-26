<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BankTransferApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $transaction;

    /**
     * Create a new notification instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
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
        $subscription = $this->transaction->subscription;
        $plan = $subscription ? $subscription->plan : null;

        $template = EmailTemplate::findBySlug('bank_transfer_approved');

        if ($template) {
            $parsed = $template->parse([
                'user_name' => $notifiable->name,
                'amount' => number_format($this->transaction->amount, 2),
                'currency' => $this->transaction->currency,
                'transaction_id' => $this->transaction->transaction_id,
                'transaction_date' => $this->transaction->transaction_date->format('M j, Y'),
                'plan_name' => $plan ? $plan->name : 'N/A',
                'next_billing' => $subscription && $subscription->renewal_at ? $subscription->renewal_at->format('M j, Y') : 'N/A',
                'billing_url' => route('user.billing'),
                'app_name' => config('app.name'),
            ]);

            return (new MailMessage)
                ->subject($parsed['subject'])
                ->view('emails.template', ['content' => $parsed['body']]);
        }

        // Fallback to blade template
        return (new MailMessage)
            ->subject('Your payment has been confirmed!')
            ->view('emails.notifications.bank-transfer-approved', [
                'user' => $notifiable,
                'transaction' => $this->transaction,
                'subscription' => $subscription,
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
            'transaction_id' => $this->transaction->id,
            'amount' => $this->transaction->amount,
            'status' => 'approved',
        ];
    }
}
