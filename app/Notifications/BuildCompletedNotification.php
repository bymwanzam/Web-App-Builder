<?php

namespace App\Notifications;

use App\Models\AppBuild;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BuildCompletedNotification extends Notification implements ShouldQueue
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
        $template = EmailTemplate::findBySlug('build_completed');

        if ($template) {
            $parsed = $template->parse([
                'user_name' => $notifiable->name,
                'app_name' => $this->build->app->name,
                'platform' => ucfirst($this->build->platform),
                'version_name' => $this->build->version_name,
                'version_code' => $this->build->version_code,
                'file_size' => $this->build->formatted_size,
                'build_time' => $this->build->formatted_duration,
                'download_url' => $this->build->download_url,
            ]);

            return (new MailMessage)
                ->subject($parsed['subject'])
                ->view('emails.template', ['content' => $parsed['body']]);
        }

        // Fallback to blade template
        return (new MailMessage)
            ->subject("Your {$this->build->app->name} build is ready!")
            ->view('emails.notifications.build-completed', [
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
        ];
    }
}
