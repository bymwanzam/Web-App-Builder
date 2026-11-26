<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BuildRequestResource;
use App\Models\AppBuild;
use App\Models\AppBuilder;
use App\Notifications\BuildCompletedNotification;
use App\Notifications\BuildFailedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BuildApiController extends Controller
{
    /**
     * Get pending builds for the requesting builder.
     */
    public function pending(Request $request, string $builderKey)
    {
        /** @var AppBuilder $builder */
        $builder = $request->attributes->get('app_builder');

        try {
            $builds = DB::transaction(function () use ($builder) {
                // Get real-time queue status from builder
                $queueUrl = "{$builder->url}:{$builder->port}/queue/{$builder->server_key}";

                try {
                    $response = Http::timeout(5)->get($queueUrl);

                    if (! $response->successful()) {
                        Log::warning('Failed to fetch queue status from builder', [
                            'server' => $builder->name,
                            'url' => $queueUrl,
                            'status' => $response->status(),
                        ]);

                        return collect(); // Fail-safe: return empty if can't reach server
                    }

                    $queueData = $response->json('data');
                    $currentProcessing = $queueData['processing'] ?? 0;
                    $availableSlots = $builder->max_queue - $currentProcessing;

                    Log::debug('Build server queue status', [
                        'server' => $builder->name,
                        'current_processing' => $currentProcessing,
                        'max_queue' => $builder->max_queue,
                        'available_slots' => $availableSlots,
                    ]);

                } catch (\Exception $e) {
                    Log::warning('Exception fetching queue status from builder', [
                        'server' => $builder->name,
                        'error' => $e->getMessage(),
                    ]);

                    return collect(); // Fail-safe: return empty if exception
                }

                if ($availableSlots <= 0) {
                    Log::debug('No available slots on builder', [
                        'server' => $builder->name,
                        'available_slots' => $availableSlots,
                    ]);

                    return collect();
                }

                // Get pending builds for this server's platform
                // Eager load relationships used by platform plugins:
                // - app: base app data and configuration
                // - app.user.plan: for feature gating (e.g., custom code permissions)
                // - app.pushNotificationConfig: for Firebase/FCM configuration
                // - app.androidWebViewConfig: for AndroidWebView platform-specific configuration
                // - app.androidWordPressConfig: for AndroidWordPress platform-specific configuration
                // - keystore: for release build signing credentials
                $builds = AppBuild::with(['app', 'app.user.plan', 'app.pushNotificationConfig', 'app.androidWebViewConfig', 'app.androidWordPressConfig', 'keystore'])
                    ->whereNull('app_builder_id')
                    ->where('status', 'pending')
                    ->whereIn('platform', $builder->platforms ?? [])
                    ->limit($availableSlots)
                    ->lockForUpdate()
                    ->get();

                Log::debug('Found pending builds', [
                    'count' => $builds->count(),
                    'builder_platforms' => $builder->platforms,
                    'build_ids' => $builds->pluck('id')->toArray(),
                ]);

                // Assign builds to this server
                foreach ($builds as $build) {
                    $build->update(['app_builder_id' => $builder->id]);
                    // Note: We use real-time queue status instead of database counter
                }

                return $builds;
            });

            Log::debug('Returning builds to builder', [
                'count' => $builds->count(),
                'builder' => $builder->name,
            ]);

            return response()->json([
                'builds' => BuildRequestResource::collection($builds),
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch pending builds for server {$builder->name}: {$e->getMessage()}");

            return response()->json([
                'error' => 'Failed to fetch pending builds',
            ], 500);
        }
    }

    /**
     * Update build status and fetch artifact from builder.
     */
    public function status(Request $request, AppBuild $build, string $builderKey)
    {
        /** @var AppBuilder $builder */
        $builder = $request->attributes->get('app_builder');

        Log::info('Build status update received', [
            'build_id' => $build->id,
            'server' => $builder->name,
            'current_status' => $build->status,
        ]);

        // Validate the build belongs to this server
        if ($build->app_builder_id !== $builder->id) {
            Log::warning('Build does not belong to requesting server', [
                'build_id' => $build->id,
                'app_builder_id' => $build->app_builder_id,
                'requesting_server_id' => $builder->id,
            ]);

            return response()->json([
                'error' => 'Build does not belong to this server',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,building_android,processing,completed,failed',
            'message' => 'nullable|string',
            'artifact_path' => 'required_if:status,completed|string',
            'error' => 'nullable|string',
            'build_logs' => 'nullable|string',
            'build_duration' => 'nullable|integer',
        ]);

        try {
            // Handle intermediate status updates
            if (in_array($validated['status'], ['pending', 'preparing', 'building_android', 'processing'])) {
                Log::info('Build status update', [
                    'build_id' => $build->id,
                    'status' => $validated['status'],
                ]);

                $build->update([
                    'status' => $validated['status'],
                ]);

                return response()->json([
                    'message' => 'Build status updated successfully',
                ]);
            }

            // Handle final statuses (completed or failed)
            if ($validated['status'] === 'completed') {
                Log::info('Processing completed build', [
                    'build_id' => $build->id,
                    'artifact_path' => $validated['artifact_path'],
                ]);

                // Update build status to processing while we fetch the artifact
                $build->update([
                    'status' => 'processing',
                    'build_logs' => $validated['build_logs'] ?? null,
                    'build_duration' => $validated['build_duration'] ?? null,
                ]);

                // Download artifact from builder
                $artifactUrl = $this->downloadArtifact($build, $builder, $validated['artifact_path']);

                if (! $artifactUrl) {
                    throw new \Exception('Failed to download build artifact');
                }

                // Calculate artifact size
                $fullPath = Storage::disk('builds')->path($artifactUrl);
                $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;

                // Generate QR code for installation
                $qrCodeService = new \App\Services\QRCodeService;
                $qrPath = $qrCodeService->generateInstallQR($build);

                // Update build to completed
                $build->update([
                    'status' => 'completed',
                    'artifact_url' => $artifactUrl,
                    'artifact_size' => $fileSize,
                    'install_qr_code' => $qrPath,
                    'completed_at' => now(),
                ]);

                Log::info('Build completed successfully', [
                    'build_id' => $build->id,
                    'artifact_url' => $artifactUrl,
                    'file_size' => $fileSize,
                ]);

                // Send success notification (don't block status update if notification fails)
                try {
                    $build->app->user->notify(new BuildCompletedNotification($build));
                } catch (\Exception $e) {
                    Log::warning('Failed to send build completion notification', [
                        'build_id' => $build->id,
                        'error' => $e->getMessage(),
                    ]);
                    // Continue processing - notification failure should not block status update
                }
            } elseif ($validated['status'] === 'failed') {
                // Build failed
                Log::warning('Build failed', [
                    'build_id' => $build->id,
                    'error' => $validated['error'] ?? $validated['message'] ?? 'Build failed',
                ]);

                $build->update([
                    'status' => 'failed',
                    'error_message' => $validated['error'] ?? $validated['message'] ?? 'Build failed',
                    'build_logs' => $validated['build_logs'] ?? null,
                    'completed_at' => now(),
                ]);

                // Send failure notification (don't block status update if notification fails)
                try {
                    $build->app->user->notify(new BuildFailedNotification($build));
                } catch (\Exception $e) {
                    Log::warning('Failed to send build failure notification', [
                        'build_id' => $build->id,
                        'error' => $e->getMessage(),
                    ]);
                    // Continue processing - notification failure should not block status update
                }
            }

            // Note: We use real-time queue status from the builder,
            // so no need to maintain a database counter

            return response()->json([
                'message' => 'Build status updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to process build status update', [
                'build_id' => $build->id,
                'server' => $builder->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Mark build as failed if something went wrong
            $build->update([
                'status' => 'failed',
                'error_message' => 'Internal server error while processing build completion',
                'completed_at' => now(),
            ]);

            // Note: We use real-time queue status, no counter to decrement

            return response()->json([
                'error' => 'Failed to process build status update',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download artifact from builder.
     */
    protected function downloadArtifact(AppBuild $build, AppBuilder $builder, string $artifactPath): ?string
    {
        try {
            // Download from builder
            $downloadUrl = "{$builder->url}:{$builder->port}/download/{$build->id}/{$builder->server_key}";

            Log::info('Downloading artifact from builder', [
                'build_id' => $build->id,
                'server' => $builder->name,
                'download_url' => $downloadUrl,
                'artifact_path' => $artifactPath,
            ]);

            $response = Http::timeout(120)->get($downloadUrl);

            if (! $response->successful()) {
                Log::error('Failed to download artifact from builder', [
                    'build_id' => $build->id,
                    'http_status' => $response->status(),
                    'response_body' => $response->body(),
                ]);

                return null;
            }

            // Prepare storage path
            $app = $build->app;
            $extension = $build->getFileExtension();
            $filename = "{$build->id}-{$build->build_type}.{$extension}";
            $buildDir = "builds/{$app->id}/{$build->id}";
            $storagePath = "{$buildDir}/{$filename}";

            // Ensure directory exists
            Storage::disk('builds')->makeDirectory($buildDir);

            // Save artifact
            Storage::disk('builds')->put($storagePath, $response->body());

            Log::info('Artifact downloaded and saved successfully', [
                'build_id' => $build->id,
                'storage_path' => $storagePath,
                'size_bytes' => strlen($response->body()),
            ]);

            return $storagePath;
        } catch (\Exception $e) {
            Log::error('Failed to download artifact', [
                'build_id' => $build->id,
                'server' => $builder->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Download keystore for release builds.
     */
    public function downloadKeystore(Request $request, string $keystoreId, string $builderKey)
    {
        /** @var AppBuilder $builder */
        $builder = $request->attributes->get('app_builder');

        try {
            $keystore = \App\Models\Keystore::find($keystoreId);

            if (! $keystore) {
                Log::warning('Keystore not found', [
                    'keystore_id' => $keystoreId,
                    'server' => $builder->name,
                ]);

                return response()->json([
                    'error' => 'Keystore not found',
                ], 404);
            }

            Log::info('Keystore download requested', [
                'keystore_id' => $keystoreId,
                'keystore_name' => $keystore->name,
                'server' => $builder->name,
            ]);

            // Get decrypted keystore data
            $keystoreData = $keystore->getKeystoreData();

            return response($keystoreData)
                ->header('Content-Type', 'application/octet-stream')
                ->header('Content-Disposition', 'attachment; filename="keystore.jks"');

        } catch (\Exception $e) {
            Log::error('Failed to download keystore', [
                'keystore_id' => $keystoreId,
                'server' => $builder->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to download keystore',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
