<?php

namespace App\Jobs;

use App\Models\AppBuild;
use App\Notifications\AdminPaymentNotification;
use App\Notifications\BuildCompletedNotification;
use App\Notifications\BuildFailedNotification;
use App\Services\QRCodeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class BuildAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public AppBuild $build;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600; // 10 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(AppBuild $build)
    {
        $this->build = $build;
        $this->onQueue('builds'); // Use dedicated builds queue
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Update build status to preparing
            $this->build->update([
                'status' => 'preparing',
                'started_at' => now(),
            ]);

            // Load app with relationships
            $app = $this->build->app()->with(['user', 'androidWebViewConfig.keystore'])->first();

            // Create build directory
            $buildDir = "builds/{$app->id}/{$this->build->id}";
            Storage::disk('builds')->makeDirectory($buildDir);

            // Update status to building Android
            $this->build->update([
                'status' => 'building_android',
            ]);

            // Simulate build process (stub - replace with actual Capacitor build commands)
            $this->executeBuild($app, $buildDir);

            // Update status to processing
            $this->build->update(['status' => 'processing']);

            // Create stub artifact file
            $artifactPath = $this->createArtifact($app, $buildDir);

            // Generate QR code
            $qrCodeService = new QRCodeService;
            $qrPath = $qrCodeService->generateInstallQR($this->build);

            // Calculate file size
            $fullPath = Storage::disk('builds')->path($artifactPath);
            $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;

            // Update build to completed
            $completedAt = now();
            $duration = $completedAt->diffInSeconds($this->build->started_at);

            $this->build->update([
                'status' => 'completed',
                'artifact_url' => $artifactPath,
                'artifact_size' => $fileSize,
                'install_qr_code' => $qrPath,
                'completed_at' => $completedAt,
                'build_duration' => $duration,
            ]);

            // Send success notification
            try {
                $app->user->notify(new BuildCompletedNotification($this->build));
            } catch (\Exception $notificationException) {
                \Log::error('Failed to send BuildCompletedNotification', [
                    'build_id' => $this->build->id,
                    'user_id' => $app->user->id,
                    'error' => $notificationException->getMessage(),
                ]);
            }

            // Send admin notification
            AdminPaymentNotification::sendIfEnabled('build_completed', $app->user, null, null, [
                'build_id' => $this->build->id,
                'app_name' => $app->name,
                'platform' => $this->build->platform,
                'version' => $this->build->version_name,
            ]);

        } catch (\Exception $e) {
            // Handle build failure
            $this->handleFailure($e);
        }
    }

    /**
     * Execute the build process (stub - simulates actual build).
     */
    protected function executeBuild($app, $buildDir): void
    {
        // Stub: Simulate build time
        // In production, this would run actual build commands:
        // - ./gradlew assembleRelease (Android)

        sleep(15); // Simulate 15 second build

        // Generate build logs (stub)
        $logs = $this->generateBuildLogs($app);
        $this->build->update(['build_logs' => $logs]);
    }

    /**
     * Create stub artifact file.
     */
    protected function createArtifact($app, $buildDir): string
    {
        // Determine file extension based on platform and format
        $extension = $this->build->getFileExtension();
        $filename = "{$this->build->id}-{$this->build->build_type}.{$extension}";
        $artifactPath = "{$buildDir}/{$filename}";

        // Create stub file with random content
        // In production, this would be the actual compiled app file
        $stubSize = 5242880; // 5MB for Android
        $stubContent = random_bytes(min($stubSize, 1048576)); // Create 1MB stub (full size would be too large)

        Storage::disk('builds')->put($artifactPath, $stubContent);

        return $artifactPath;
    }

    /**
     * Generate build logs (stub).
     */
    protected function generateBuildLogs($app): string
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $platform = ucfirst($this->build->platform);
        $extension = $this->build->getFileExtension();

        // Build format info for Android
        $formatInfo = '';
        if ($this->build->platform === 'android') {
            $format = strtoupper($this->build->build_format ?? 'apk');
            $formatInfo = "\n[$timestamp] Output format: {$format}";
        }

        return <<<LOGS
[$timestamp] Starting {$platform} build for {$app->name}
[$timestamp] Version: {$this->build->version_name} (Build #{$this->build->version_code})
[$timestamp] Build type: {$this->build->build_type}{$formatInfo}
[$timestamp]
[$timestamp] Preparing build environment...
[$timestamp] ✓ Configuration validated
[$timestamp] ✓ Dependencies resolved
[$timestamp]
[$timestamp] Compiling {$platform} application...
[$timestamp] ✓ Assets compiled
[$timestamp] ✓ Resources packaged
[$timestamp] ✓ Application signed
[$timestamp]
[$timestamp] Build completed successfully!
[$timestamp] Output: {$this->build->id}-{$this->build->build_type}.{$extension}
LOGS;
    }

    /**
     * Handle build failure.
     */
    protected function handleFailure(\Exception $e): void
    {
        $this->build->update([
            'status' => 'failed',
            'error_message' => $e->getMessage(),
            'completed_at' => now(),
        ]);

        // Send failure notification
        try {
            $this->build->app->user->notify(new BuildFailedNotification($this->build));
        } catch (\Exception $notificationException) {
            \Log::error('Failed to send BuildFailedNotification', [
                'build_id' => $this->build->id,
                'user_id' => $this->build->app->user->id,
                'error' => $notificationException->getMessage(),
            ]);
        }

        // Send admin notification
        $app = $this->build->app;
        AdminPaymentNotification::sendIfEnabled('build_failed', $app->user, null, null, [
            'build_id' => $this->build->id,
            'app_name' => $app->name,
            'platform' => $this->build->platform,
            'error' => $e->getMessage(),
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->handleFailure($exception);
    }
}
