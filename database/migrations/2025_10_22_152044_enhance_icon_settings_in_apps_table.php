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
        // Add new icon ZIP upload fields if they don't exist
        if (! Schema::hasColumn('apps', 'uploaded_icon_zip')) {
            Schema::table('apps', function (Blueprint $table) {
                $table->string('uploaded_icon_zip')
                    ->nullable()
                    ->after('custom_js');
                $table->string('icon_extracted_path')
                    ->nullable()
                    ->after('uploaded_icon_zip');
            });
        }

        // Remove legacy icon gradient fields (icon_type already removed by earlier migration)
        if (Schema::hasColumn('apps', 'icon_background_gradient_start')) {
            Schema::table('apps', function (Blueprint $table) {
                $table->dropColumn([
                    'icon_background_gradient_start',
                    'icon_background_gradient_end',
                ]);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            // Restore legacy icon fields
            $table->string('icon_type')->nullable();
            $table->string('icon_background_gradient_start')->nullable();
            $table->string('icon_background_gradient_end')->nullable();

            // Remove new fields
            $table->dropColumn(['uploaded_icon_zip', 'icon_extracted_path']);
        });
    }
};
