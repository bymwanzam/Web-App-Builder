<?php

namespace App\Livewire\Shared;

use App\Models\AppBuild;
use App\Models\AppetizeUsage;
use App\Services\AppetizeService;
use Livewire\Component;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AppPreview extends Component
{
    public AppBuild $build;

    public string $platform;

    public bool $canUseAppetize = false;

    public ?string $previewUrl = null;

    public array $sessionConfig = [];

    public array $manualTestSteps = [];

    public int $remainingMinutes = 0;

    protected AppetizeService $appetizeService;

    public function boot(AppetizeService $appetizeService)
    {
        $this->appetizeService = $appetizeService;
    }

    public function mount()
    {
        // Check if user's plan allows Appetize
        $this->canUseAppetize = $this->checkAppetizeAccess();

        if ($this->canUseAppetize && $this->build->appetize_public_key) {
            $this->previewUrl = $this->appetizeService->buildPreviewUrl($this->build);
            $this->sessionConfig = $this->appetizeService->getSessionConfig($this->build);
            $this->remainingMinutes = $this->getRemainingMinutes();
        } else {
            $this->manualTestSteps = $this->getManualTestSteps();
        }
    }

    /**
     * Check if user has Appetize access via their plan
     */
    protected function checkAppetizeAccess(): bool
    {
        $user = auth()->user();

        if (! $user->plan) {
            return false;
        }

        return $user->plan->can_use_appetize ?? false;
    }

    /**
     * Get remaining Appetize minutes for current user
     */
    public function getRemainingMinutes(): int
    {
        $user = auth()->user();
        $plan = $user->plan;

        if (! $plan || ! $plan->can_use_appetize) {
            return 0;
        }

        // Unlimited minutes
        if ($plan->monthly_appetize_minutes === -1) {
            return 999999;
        }

        // Calculate used minutes this month
        $usedMinutes = AppetizeUsage::where('user_id', $user->id)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('minutes_used');

        $totalMinutes = $plan->monthly_appetize_minutes ?? 0;

        return max(0, $totalMinutes - $usedMinutes);
    }

    /**
     * Generate manual testing instructions for users without Appetize access
     */
    protected function getManualTestSteps(): array
    {
        return [
            [
                'number' => 1,
                'title' => 'Scan or Download File',
                'description' => "Scan the QR code or download the file to test on {$this->getPlatformName()}.",
                'action' => 'download',
                'qr_code' => $this->build->install_qr_code,
                'download_url' => $this->build->download_url,
            ],
            [
                'number' => 2,
                'title' => 'Go to appetize.io',
                'description' => 'Login & upload the download file to simulate the app.',
                'action' => 'external_link',
                'url' => 'https://appetize.io/upload',
            ],
            [
                'number' => 3,
                'title' => 'Try Demo',
                'description' => "Launch the app & explore the demo. It's available for 14 days.",
                'action' => 'info',
            ],
        ];
    }

    /**
     * Get human-readable platform name
     */
    protected function getPlatformName(): string
    {
        try {
            $platform = $this->build->app->getPlatformInstance();

            return $platform ? $platform->getName() : ucfirst($this->platform);
        } catch (\Exception $e) {
            return ucfirst($this->platform);
        }
    }

    /**
     * Generate QR code SVG for download URL
     */
    public function getQrCodeSvgProperty(): ?string
    {
        if (! $this->build->download_url) {
            return null;
        }

        return QrCode::size(128)
            ->margin(0)
            ->errorCorrection('H')
            ->generate($this->build->download_url);
    }

    /**
     * Format file size for display
     */
    public function formatFileSize(?int $bytes): string
    {
        if (! $bytes) {
            return 'Unknown size';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2).' '.$units[$pow];
    }

    /**
     * Track session start
     */
    public function trackSessionStart()
    {
        AppetizeUsage::create([
            'user_id' => auth()->id(),
            'app_build_id' => $this->build->id,
            'session_started_at' => now(),
        ]);
    }

    public function render()
    {
        if ($this->canUseAppetize && $this->previewUrl) {
            return view('livewire.shared.app-preview-enabled');
        }

        return view('livewire.shared.app-preview-fallback');
    }
}
