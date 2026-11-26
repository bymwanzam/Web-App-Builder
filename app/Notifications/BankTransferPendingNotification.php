<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BankTransferPendingNotification extends Notification implements ShouldQueue
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
        $user = $this->transaction->user;
        $subscription = $this->transaction->subscription;

        $template = EmailTemplate::findBySlug('bank_transfer_pending');

        if ($template) {
            $parsed = $template->parse([
                'user_name' => $user->name,
                'user_email' => $user->email,
                'amount' => number_format($this->transaction->amount, 2),
                'currency' => $this->transaction->currency,
                'transaction_id' => $this->transaction->transaction_id,
                'plan_name' => $subscription && $subscription->plan ? $subscription->plan->name : 'N/A',
                'review_url' => route('admin.transactions'),
                'app_name' => config('app.name'),
            ]);

            return (new MailMessage)
                ->subject($parsed['subject'])
                ->view('emails.template', ['content' => $parsed['body']]);
        }

        // Fallback to blade template
        return (new MailMessage)
            ->subject('New Bank Transfer Payment Pending')
            ->view('emails.notifications.bank-transfer-pending', [
                'user' => $notifiable,
                'transaction' => $this->transaction,
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
            'user_id' => $this->transaction->user_id,
        ];
    }
}
