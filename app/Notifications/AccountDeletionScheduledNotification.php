<?php

namespace App\Notifications;

use App\Models\AccountDeletionRequest;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountDeletionScheduledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public AccountDeletionRequest $deletionRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(AccountDeletionRequest $deletionRequest)
    {
        $this->deletionRequest = $deletionRequest;
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
        $scheduledAt = $this->deletionRequest->scheduled_at->format('F j, Y \a\t g:i A');
        $cancelUrl = url('/account/cancel-deletion/'.$this->deletionRequest->cancellation_token);

        $template = EmailTemplate::findBySlug('account_deletion_scheduled');

        if ($template) {
            $parsed = $template->parse([
                'user_name' => $notifiable->name,
                'deletion_date' => $scheduledAt,
                'cancel_url' => $cancelUrl,
                'app_name' => config('app.name'),
            ]);

            return (new MailMessage)
                ->subject($parsed['subject'])
                ->view('emails.template', ['content' => $parsed['body']]);
        }

        // Fallback to inline message
        return (new MailMessage)
            ->subject('Account Deletion Scheduled')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('We received a request to delete your account.')
            ->line('Your account and all associated data will be permanently deleted on '.$scheduledAt.'.')
            ->line('If you did not request this or have changed your mind, you can cancel the deletion using the button below.')
            ->action('Cancel Account Deletion', $cancelUrl)
            ->line('If you do nothing, your account will be automatically deleted on the scheduled date.')
            ->salutation('Best regards,');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'deletion_request_id' => $this->deletionRequest->id,
            'scheduled_at' => $this->deletionRequest->scheduled_at?->toIso8601String(),
        ];
    }
}
