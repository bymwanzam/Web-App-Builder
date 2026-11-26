<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('app_builds', function (Blueprint $table) {
            $table->string('appetize_public_key', 100)
                ->nullable()
                ->after('install_qr_code')
                ->comment('Appetize.io app identifier');

            $table->string('appetize_preview_url', 500)
                ->nullable()
                ->after('appetize_public_key')
                ->comment('Full embed URL with config params');

            $table->timestamp('appetize_uploaded_at')
                ->nullable()
                ->after('appetize_preview_url')
                ->comment('When build was uploaded to Appetize');

            $table->index('appetize_public_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_builds', function (Blueprint $table) {
            $table->dropIndex(['appetize_public_key']);
            $table->dropColumn([
                'appetize_public_key',
                'appetize_preview_url',
                'appetize_uploaded_at',
            ]);
        });
    }
};
