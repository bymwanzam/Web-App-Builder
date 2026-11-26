<?php

namespace App\Livewire\User;

use App\Jobs\ProcessDataExport;
use App\Models\DataExportRequest;
use App\Models\SystemSetting;
use Livewire\Component;

class DataExportCard extends Component
{
    public function requestExport(): void
    {
        $user = auth()->user();

        // Check rate limit (default 24 hours)
        $rateLimitHours = SystemSetting::get('data_export_rate_limit_hours', 24);
        $lastRequest = DataExportRequest::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHours($rateLimitHours))
            ->first();

        if ($lastRequest) {
            $nextAllowed = $lastRequest->created_at->addHours($rateLimitHours);
            $this->dispatch('toast', type: 'error', message: 'You can request a new export after '.$nextAllowed->diffForHumans().'.');

            return;
        }

        // Create new export request
        $request = DataExportRequest::createForUser($user);

        // Dispatch job to process export
        ProcessDataExport::dispatch($request);

        $this->dispatch('toast', type: 'success', message: 'Data export requested. You will receive an email when it\'s ready.');
    }

    public function render()
    {
        $user = auth()->user();

        // Get recent export requests
        $exportRequests = DataExportRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Check if export is enabled
        $exportEnabled = SystemSetting::get('data_export_enabled', true);

        // Check if can request new export (rate limit)
        $rateLimitHours = SystemSetting::get('data_export_rate_limit_hours', 24);
        $canRequestExport = ! DataExportRequest::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHours($rateLimitHours))
            ->exists();

        // Check if there's an active (pending/processing) export
        $hasActiveExport = DataExportRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'processing'])
            ->exists();

        return view('livewire.user.data-export-card', [
            'exportRequests' => $exportRequests,
            'exportEnabled' => $exportEnabled,
            'canRequestExport' => $canRequestExport && ! $hasActiveExport,
            'hasActiveExport' => $hasActiveExport,
        ]);
    }
}
