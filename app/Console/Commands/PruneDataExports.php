<?php

namespace App\Console\Commands;

use App\Models\DataExportRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PruneDataExports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:prune-exports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune expired data export files and requests';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiredExports = DataExportRequest::where('expires_at', '<', now())
            ->where('status', 'completed')
            ->get();

        $count = $expiredExports->count();

        foreach ($expiredExports as $export) {
            // Delete the file if it exists
            if ($export->file_path && Storage::disk('local')->exists($export->file_path)) {
                Storage::disk('local')->delete($export->file_path);
            }

            // Delete the request record
            $export->delete();
        }

        $this->info("Pruned {$count} expired data export(s).");

        return Command::SUCCESS;
    }
}
