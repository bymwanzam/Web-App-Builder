<?php

namespace App\Notifications;

use App\Models\AppBuild;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class BuildFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public AppBuild $build;

    /**
     * Create a new notification instance.
     */
    public function __construct(AppBuild $build)
    {
        $this->build = $build;
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
        $template = EmailTemplate::findBySlug('build_failed');

        if ($template) {
            $parsed = $template->parse([
                'user_name' => $notifiable->name,
                'app_name' => $this->build->app->name,
                'platform' => ucfirst($this->build->platform),
                'version_name' => $this->build->version_name,
                'error_message' => Str::limit($this->build->error_message ?? 'Unknown error', 200),
            ]);

            return (new MailMessage)
                ->error()
                ->subject($parsed['subject'])
                ->view('emails.template', ['content' => $parsed['body']]);
        }

        // Fallback to blade template
        return (new MailMessage)
            ->subject("Build failed for {$this->build->app->name}")
            ->view('emails.notifications.build-failed', [
                'user' => $notifiable,
                'build' => $this->build,
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
            'build_id' => $this->build->id,
            'app_id' => $this->build->app_id,
            'platform' => $this->build->platform,
            'version' => $this->build->version_name,
            'status' => $this->build->status,
            'error' => $this->build->error_message,
        ];
    }
}
