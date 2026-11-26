<?php

namespace App\Plugins\Platforms\AndroidWordPress\Livewire;

use App\Models\AppBuild;
use Livewire\Attributes\On;
use Livewire\Component;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BuildDownloadModal extends Component
{
    public $showModal = false;

    public $build = null;

    #[On('download-build')]
    public function showDownload($id)
    {
        $this->build = AppBuild::findOrFail($id);

        // Ensure user owns this build's app
        if ($this->build->app->user_id !== auth()->id()) {
            abort(403);
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->build = null;
    }

    public function getQrCodeSvgProperty()
    {
        if (! $this->build) {
            return null;
        }

        // Generate download URL (signed URL for public access)
        $downloadUrl = $this->build->download_url;

        // Generate QR code as SVG with no margin (wrapper provides padding)
        return QrCode::size(200)
            ->margin(0)
            ->errorCorrection('H')
            ->generate($downloadUrl);
    }

    public function render()
    {
        return view('plugins.android-wordpress::livewire.build-download-modal');
    }
}
