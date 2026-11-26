<?php

namespace App\Services;

use App\Models\DataExportRequest;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DataExportService
{
    public function generateExport(DataExportRequest $request): void
    {
        try {
            $user = $request->user;

            if (! $user) {
                $this->markAsFailed($request, 'User not found');

                return;
            }

            $request->update(['status' => 'processing']);

            $fileName = 'exports/user_'.$user->id.'_'.time().'.zip';

            // Create ZIP in system temp directory (works with Storage::fake)
            $tempPath = sys_get_temp_dir().'/'.uniqid('export_').'.zip';

            $zip = new ZipArchive;
            if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                $this->markAsFailed($request, 'Failed to create export file');

                return;
            }

            // Add profile data
            $zip->addFromString('profile.json', json_encode($this->getProfileData($user), JSON_PRETTY_PRINT));

            // Add apps data
            $zip->addFromString('apps.json', json_encode($this->getAppsData($user), JSON_PRETTY_PRINT));

            // Add consents data
            $zip->addFromString('consents.json', json_encode($this->getConsentsData($user), JSON_PRETTY_PRINT));

            // Add subscriptions data
            $zip->addFromString('subscriptions.json', json_encode($this->getSubscriptionsData($user), JSON_PRETTY_PRINT));

            // Add transactions data
            $zip->addFromString('transactions.json', json_encode($this->getTransactionsData($user), JSON_PRETTY_PRINT));

            // Add notifications data
            $zip->addFromString('notifications.json', json_encode($this->getNotificationsData($user), JSON_PRETTY_PRINT));

            // Add AI conversations data
            $zip->addFromString('ai_conversations.json', json_encode($this->getAiConversationsData($user), JSON_PRETTY_PRINT));

            $zip->close();

            // Get file size and contents
            $fileSize = filesize($tempPath);
            $contents = file_get_contents($tempPath);

            // Store via Storage facade (works with fake storage)
            Storage::disk('local')->put($fileName, $contents);

            // Clean up temp file
            @unlink($tempPath);

            $request->update([
                'status' => 'completed',
                'file_path' => $fileName,
                'file_size' => $fileSize,
                'completed_at' => now(),
            ]);

        } catch (\Exception $e) {
            $this->markAsFailed($request, $e->getMessage());
        }
    }

    protected function markAsFailed(DataExportRequest $request, string $message): void
    {
        $request->update([
            'status' => 'failed',
            'error_message' => $message,
        ]);
    }

    protected function getProfileData($user): array
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at?->toIso8601String(),
            'role' => $user->role,
            'status' => $user->status,
            'created_at' => $user->created_at?->toIso8601String(),
            'updated_at' => $user->updated_at?->toIso8601String(),
        ];
    }

    protected function getAppsData($user): array
    {
        return ($user->apps ?? collect())->map(function ($app) {
            return [
                'id' => $app->id,
                'name' => $app->name,
                'platform' => $app->platform,
                'created_at' => $app->created_at?->toIso8601String(),
                'updated_at' => $app->updated_at?->toIso8601String(),
            ];
        })->toArray();
    }

    protected function getConsentsData($user): array
    {
        return ($user->consents ?? collect())->map(function ($consent) {
            return [
                'consent_type' => $consent->consent_type,
                'consented' => $consent->consented,
                'version' => $consent->version,
                'consented_at' => $consent->consented_at?->toIso8601String(),
                'withdrawn_at' => $consent->withdrawn_at?->toIso8601String(),
            ];
        })->toArray();
    }

    protected function getSubscriptionsData($user): array
    {
        return ($user->subscriptions ?? collect())->map(function ($subscription) {
            return [
                'id' => $subscription->id,
                'plan_id' => $subscription->plan_id,
                'status' => $subscription->status,
                'amount' => $subscription->amount,
                'payment_method' => $subscription->payment_method,
                'renewal_at' => $subscription->renewal_at?->toIso8601String(),
                'ends_at' => $subscription->ends_at?->toIso8601String(),
                'created_at' => $subscription->created_at?->toIso8601String(),
            ];
        })->toArray();
    }

    protected function getTransactionsData($user): array
    {
        return ($user->transactions ?? collect())->map(function ($transaction) {
            return [
                'transaction_id' => $transaction->transaction_id,
                'amount' => $transaction->amount,
                'currency' => $transaction->currency,
                'status' => $transaction->status,
                'payment_method' => $transaction->payment_method,
                'transaction_date' => $transaction->transaction_date?->toIso8601String(),
            ];
        })->toArray();
    }

    protected function getNotificationsData($user): array
    {
        $notifications = [];

        foreach ($user->apps ?? [] as $app) {
            if (! method_exists($app, 'notifications')) {
                continue;
            }
            foreach ($app->notifications ?? [] as $notification) {
                $notifications[] = [
                    'app_name' => $app->name,
                    'title' => $notification->title,
                    'body' => $notification->body,
                    'status' => $notification->status,
                    'sent_at' => $notification->sent_at?->toIso8601String(),
                    'created_at' => $notification->created_at?->toIso8601String(),
                ];
            }
        }

        return $notifications;
    }

    protected function getAiConversationsData($user): array
    {
        if (! method_exists($user, 'aiConversations')) {
            return [];
        }

        return $user->aiConversations->map(function ($conversation) {
            return [
                'id' => $conversation->id,
                'title' => $conversation->title ?? null,
                'messages_count' => is_array($conversation->messages) ? count($conversation->messages) : 0,
                'created_at' => $conversation->created_at?->toIso8601String(),
                'updated_at' => $conversation->updated_at?->toIso8601String(),
            ];
        })->toArray();
    }
}
