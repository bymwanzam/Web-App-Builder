<?php

namespace App\Notifications;

use App\Models\DataExportRequest;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DataExportReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public DataExportRequest $exportRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(DataExportRequest $exportRequest)
    {
        $this->exportRequest = $exportRequest;
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
        $downloadUrl = route('data-export.download', ['token' => $this->exportRequest->download_token]);
        $expiresAt = $this->exportRequest->expires_at->format('F j, Y \a\t g:i A');
        $fileSize = $this->formatBytes($this->exportRequest->file_size);

        $template = EmailTemplate::findBySlug('data_export_ready');

        if ($template) {
            $parsed = $template->parse([
                'user_name' => $notifiable->name,
                'download_url' => $downloadUrl,
                'expires_at' => $expiresAt,
                'file_size' => $fileSize,
                'app_name' => config('app.name'),
            ]);

            return (new MailMessage)
                ->subject($parsed['subject'])
                ->view('emails.template', ['content' => $parsed['body']]);
        }

        // Fallback to inline message
        return (new MailMessage)
            ->subject('Your Data Export is Ready')
            ->greeting('Hello '.$notifiable->name.'!')
            ->line('Your requested data export has been completed and is ready for download.')
            ->line('File size: '.$fileSize)
            ->action('Download Your Data', $downloadUrl)
            ->line('This download link will expire on '.$expiresAt.'.')
            ->line('For your security, please download your data before the link expires.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'export_request_id' => $this->exportRequest->id,
            'file_size' => $this->exportRequest->file_size,
            'expires_at' => $this->exportRequest->expires_at?->toIso8601String(),
        ];
    }

    /**
     * Format bytes to human readable format.
     */
    protected function formatBytes(?int $bytes): string
    {
        if (! $bytes) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        return round($bytes / pow(1024, $pow), 2).' '.$units[$pow];
    }
}
