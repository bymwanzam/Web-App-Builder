<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $language = Language::create([
            'name' => 'English',
            'code' => 'en',
            'flag' => '🇺🇸',
            'status' => 'active',
        ]);

        // Create translation files for English
        $language->createTranslationFiles();
    }
}
