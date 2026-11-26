<?php

namespace App\Console\Commands;

use App\Models\AccountDeletionRequest;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessAccountDeletions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:process-deletions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending account deletion requests that have passed their grace period';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Processing account deletion requests...');

        $requests = AccountDeletionRequest::where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->get();

        $processed = 0;
        $failed = 0;

        foreach ($requests as $request) {
            try {
                DB::transaction(function () use ($request) {
                    $user = $request->user;

                    if (! $user) {
                        $request->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                        ]);

                        return;
                    }

                    // Log the deletion
                    Log::info('Processing account deletion', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'request_id' => $request->id,
                    ]);

                    // Mark request as completed before deleting user
                    // (cascade will delete the request, but we want to update status first)
                    $request->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                    ]);

                    // Delete the user (cascades to related data)
                    $user->delete();
                });

                $processed++;
                $this->line("  ✓ Processed request #{$request->id}");

            } catch (\Exception $e) {
                $failed++;
                $this->error("  ✗ Failed to process request #{$request->id}: {$e->getMessage()}");

                Log::error('Account deletion failed', [
                    'request_id' => $request->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Completed: {$processed} processed, {$failed} failed");

        return self::SUCCESS;
    }
}
