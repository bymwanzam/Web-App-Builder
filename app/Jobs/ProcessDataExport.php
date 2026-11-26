<?php

namespace App\Jobs;

use App\Models\DataExportRequest;
use App\Notifications\DataExportReadyNotification;
use App\Services\DataExportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessDataExport implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public DataExportRequest $request
    ) {}

    public function handle(DataExportService $service): void
    {
        $service->generateExport($this->request);

        // Send notification if export completed successfully
        $this->request->refresh();

        if ($this->request->status === 'completed' && $this->request->user) {
            $this->request->user->notify(new DataExportReadyNotification($this->request));
        }
    }
}
