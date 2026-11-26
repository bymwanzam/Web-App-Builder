<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BankTransferDeclinedNotification extends Notification implements ShouldQueue
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
        $template = EmailTemplate::findBySlug('bank_transfer_declined');

        if ($template) {
            $parsed = $template->parse([
                'user_name' => $notifiable->name,
                'amount' => number_format($this->transaction->amount, 2),
                'currency' => $this->transaction->currency,
                'transaction_id' => $this->transaction->transaction_id,
                'billing_url' => route('user.billing'),
                'app_name' => config('app.name'),
            ]);

            return (new MailMessage)
                ->error()
                ->subject($parsed['subject'])
                ->view('emails.template', ['content' => $parsed['body']]);
        }

        // Fallback to blade template
        return (new MailMessage)
            ->subject('Payment verification unsuccessful')
            ->view('emails.notifications.bank-transfer-declined', [
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
            'status' => 'declined',
        ];
    }
}
