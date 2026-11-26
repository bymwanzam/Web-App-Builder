<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all 'installed' status to 'inactive'
        DB::table('plugins')
            ->where('status', 'installed')
            ->update(['status' => 'inactive']);

        // SQLite doesn't support modifying enums directly, but the change is semantic
        // The actual enum constraint will be updated in the original migration file
        // For production databases (MySQL/PostgreSQL), you would need to ALTER the column
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'inactive' status back to 'installed' for unconfigured plugins
        DB::table('plugins')
            ->where('status', 'inactive')
            ->whereNull('config')
            ->update(['status' => 'installed']);
    }
};
