<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountDeletionCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

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
        $template = EmailTemplate::findBySlug('account_deletion_cancelled');

        if ($template) {
            $parsed = $template->parse([
                'user_name' => $notifiable->name,
                'dashboard_url' => route('user.apps'),
                'app_name' => config('app.name'),
            ]);

            return (new MailMessage)
                ->subject($parsed['subject'])
                ->view('emails.template', ['content' => $parsed['body']]);
        }

        // Fallback to inline message
        return (new MailMessage)
            ->subject('Account Deletion Cancelled')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Your account deletion request has been successfully cancelled.')
            ->line('Your account will remain active and no data will be deleted.')
            ->line('If you did not cancel this request or if you have any concerns, please contact our support team.')
            ->salutation('Best regards,');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
