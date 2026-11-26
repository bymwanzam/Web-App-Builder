<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InactiveAccountWarningNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected int $retentionDays;

    protected Carbon $deletionDate;

    /**
     * Create a new notification instance.
     */
    public function __construct(int $retentionDays, Carbon $deletionDate)
    {
        $this->retentionDays = $retentionDays;
        $this->deletionDate = $deletionDate;
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
        $lastLogin = $notifiable->last_login_at
            ? $notifiable->last_login_at->format('F j, Y')
            : 'Never';

        $deletionDate = $this->deletionDate->format('F j, Y');

        $template = EmailTemplate::findBySlug('inactive_account_warning');

        if ($template) {
            $parsed = $template->parse([
                'user_name' => $notifiable->name,
                'last_login' => $lastLogin,
                'retention_days' => $this->retentionDays,
                'deletion_date' => $deletionDate,
                'login_url' => route('login'),
                'app_name' => config('app.name'),
            ]);

            return (new MailMessage)
                ->subject($parsed['subject'])
                ->view('emails.template', ['content' => $parsed['body']]);
        }

        // Fallback to inline message
        return (new MailMessage)
            ->subject('Your account will be deleted due to inactivity')
            ->greeting('Hello '.$notifiable->name.',')
            ->line("We noticed you haven't logged into your account for a while.")
            ->line("In accordance with our data retention policy, inactive accounts are automatically deleted after {$this->retentionDays} days of inactivity.")
            ->line("Last login: {$lastLogin}")
            ->line("Your account is scheduled for deletion on {$deletionDate}.")
            ->action('Log In to Keep Your Account', route('login'))
            ->line('Simply log in before the scheduled date to keep your account active.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'retention_days' => $this->retentionDays,
            'deletion_date' => $this->deletionDate->toIso8601String(),
        ];
    }
}
