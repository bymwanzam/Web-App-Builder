<?php

namespace App\Services;

use App\Models\App;
use App\Models\AppNotification;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class PushNotificationService
{
    /**
     * Send a notification to an app's topic
     */
    public function sendToApp(AppNotification $appNotification): array
    {
        $app = $appNotification->app;
        $pushConfig = $app->pushNotificationConfig;

        if (! $pushConfig || ! $pushConfig->enabled) {
            return [
                'success' => false,
                'message' => 'Push notifications are not enabled for this app.',
            ];
        }

        if (! $pushConfig->firebase_credentials_file) {
            return [
                'success' => false,
                'message' => 'Firebase credentials not configured for this app.',
            ];
        }

        try {
            $messaging = $this->getMessagingForApp($app);

            // Build the notification
            $notification = Notification::create(
                $appNotification->title,
                $appNotification->body
            );

            // Add image if provided
            if ($appNotification->image_url && $appNotification->notification_style === 'image') {
                $notification = $notification->withImageUrl($appNotification->image_url);
            }

            // Create the message for topic
            $topic = $this->getTopicForApp($app);
            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification($notification)
                ->withAndroidConfig(
                    AndroidConfig::fromArray([
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'default',
                            'channel_id' => 'default',
                        ],
                    ])
                );

            // Send the message
            $response = $messaging->send($message);

            $appNotification->markAsSent($response);

            return [
                'success' => true,
                'message' => 'Notification sent successfully!',
                'response' => $response,
            ];

        } catch (\Exception $e) {
            $appNotification->markAsFailed($e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to send notification: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Send a test notification to verify Firebase configuration
     */
    public function sendTestNotification(App $app, string $title, string $body): array
    {
        $pushConfig = $app->pushNotificationConfig;

        if (! $pushConfig || ! $pushConfig->firebase_credentials_file) {
            return [
                'success' => false,
                'message' => 'Firebase credentials not configured.',
            ];
        }

        try {
            $messaging = $this->getMessagingForApp($app);

            $notification = Notification::create($title, $body);
            $topic = $this->getTopicForApp($app);

            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification($notification);

            $response = $messaging->send($message);

            return [
                'success' => true,
                'message' => 'Test notification sent successfully!',
                'response' => $response,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Validate Firebase credentials for an app
     */
    public function validateCredentials(App $app): array
    {
        $pushConfig = $app->pushNotificationConfig;

        if (! $pushConfig || ! $pushConfig->firebase_credentials_file) {
            return [
                'valid' => false,
                'message' => 'Firebase credentials not configured.',
            ];
        }

        try {
            // Try to get the messaging instance - this will validate the credentials
            $messaging = $this->getMessagingForApp($app);

            // Try to validate a dummy condition to test the connection
            $messaging->validateMessage(
                CloudMessage::withTarget('topic', 'test')
                    ->withNotification(Notification::create('Test', 'Test'))
            );

            return [
                'valid' => true,
                'message' => 'Firebase credentials are valid.',
            ];

        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => 'Invalid credentials: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Get Firebase Messaging instance for a specific app
     */
    protected function getMessagingForApp(App $app)
    {
        $pushConfig = $app->pushNotificationConfig;
        $credentialsPath = Storage::disk('private')->path($pushConfig->firebase_credentials_file);

        if (! file_exists($credentialsPath)) {
            throw new \Exception('Firebase credentials file not found.');
        }

        $factory = (new Factory)->withServiceAccount($credentialsPath);

        return $factory->createMessaging();
    }

    /**
     * Get the FCM topic name for an app
     * Uses the app's package name as the topic
     */
    protected function getTopicForApp(App $app): string
    {
        // Get package name from platform config
        $config = $app->getConfig();
        $packageName = $config?->package_name;

        if (! $packageName) {
            throw new \Exception('App package name not configured.');
        }

        // Use package name with underscores instead of dots for FCM topic
        return str_replace('.', '_', $packageName);
    }

    /**
     * Process all due scheduled notifications
     */
    public function processScheduledNotifications(): int
    {
        $notifications = AppNotification::dueForSending()->get();
        $count = 0;

        foreach ($notifications as $notification) {
            $result = $this->sendToApp($notification);
            if ($result['success']) {
                $count++;
            }
        }

        return $count;
    }
}
