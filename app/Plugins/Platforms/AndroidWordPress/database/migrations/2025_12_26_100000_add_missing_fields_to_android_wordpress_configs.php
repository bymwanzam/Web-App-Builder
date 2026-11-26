<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('android_wordpress_configs', function (Blueprint $table) {
            // Add excerpt_length field for content settings
            if (! Schema::hasColumn('android_wordpress_configs', 'excerpt_length')) {
                $table->integer('excerpt_length')->default(150)->after('show_reading_time');
            }

            // Add progress indicator fields for design settings
            if (! Schema::hasColumn('android_wordpress_configs', 'progress_indicator')) {
                $table->enum('progress_indicator', ['disable', 'linear', 'circular'])
                    ->default('linear')
                    ->after('show_app_bar_title');
            }

            if (! Schema::hasColumn('android_wordpress_configs', 'progress_indicator_color')) {
                $table->string('progress_indicator_color')->nullable()->after('progress_indicator');
            }
        });

        // Update splashscreen_animation enum to include 'slide'
        // This requires raw SQL for MySQL/MariaDB or SQLite handling
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE android_wordpress_configs MODIFY COLUMN splashscreen_animation ENUM('fade', 'zoom', 'slide', 'none') DEFAULT 'fade'");
        } elseif ($driver === 'sqlite') {
            // SQLite doesn't support ENUM modification directly
            // For SQLite, we'll just add the constraint check in validation (already done in Livewire)
            // The column is stored as text in SQLite anyway
        }
    }

    public function down(): void
    {
        Schema::table('android_wordpress_configs', function (Blueprint $table) {
            $columns = ['excerpt_length', 'progress_indicator', 'progress_indicator_color'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('android_wordpress_configs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Revert splashscreen_animation enum (optional - skip for simplicity)
    }
};
