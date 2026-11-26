<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class IconExtractionService
{
    /**
     * Extract and validate icon ZIP file from icon.kitchen
     *
     * @param  string  $zipPath  Path to the uploaded ZIP file
     * @param  string  $appId  The app ID for storage organization
     * @return array Result with success status and message
     */
    public function extractAndValidate($zipPath, $appId)
    {
        $zip = new ZipArchive;

        if ($zip->open($zipPath) !== true) {
            return [
                'success' => false,
                'message' => 'Failed to open ZIP file. Please ensure the file is a valid ZIP archive.',
            ];
        }

        // Create temporary extraction directory
        $tempDir = storage_path('app/temp/icons/'.uniqid());
        File::ensureDirectoryExists($tempDir);

        // Extract ZIP contents
        $zip->extractTo($tempDir);
        $zip->close();

        // Validate structure
        $validation = $this->validateIconStructure($tempDir);

        if (! $validation['valid']) {
            // Clean up temp directory
            File::deleteDirectory($tempDir);

            return [
                'success' => false,
                'message' => $validation['message'],
            ];
        }

        // Move to permanent storage
        $permanentPath = 'icons/'.$appId.'/extracted';
        Storage::disk('public')->deleteDirectory($permanentPath);
        Storage::disk('public')->makeDirectory($permanentPath);

        // Copy files to public storage
        $this->copyDirectory($tempDir, storage_path('app/public/'.$permanentPath));

        // Copy primary icon for app display (use web/icon-512.png)
        $appIconPath = null;
        $sourceIconPath = $tempDir.'/web/icon-512.png';

        if (File::exists($sourceIconPath)) {
            $appIconPath = 'icons/'.$appId.'/app-icon.png';
            Storage::disk('public')->put(
                $appIconPath,
                File::get($sourceIconPath)
            );
        }

        // Clean up temp directory
        File::deleteDirectory($tempDir);

        return [
            'success' => true,
            'message' => 'Icon successfully extracted and validated.',
            'extracted_path' => $permanentPath,
            'app_icon_path' => $appIconPath,
        ];
    }

    /**
     * Validate the icon ZIP structure
     *
     * @param  string  $dir  Directory to validate
     * @return array Validation result
     */
    protected function validateIconStructure($dir)
    {
        // Check for required directories
        $requiredDirs = ['android', 'web'];
        $missingDirs = [];

        foreach ($requiredDirs as $requiredDir) {
            if (! File::isDirectory($dir.'/'.$requiredDir)) {
                $missingDirs[] = $requiredDir;
            }
        }

        if (! empty($missingDirs)) {
            return [
                'valid' => false,
                'message' => 'Invalid icon.kitchen ZIP structure. Missing directories: '.implode(', ', $missingDirs),
            ];
        }

        // Validate Android structure
        if (! File::isDirectory($dir.'/android/res')) {
            return [
                'valid' => false,
                'message' => 'Invalid Android icon structure. Missing android/res directory.',
            ];
        }

        // Check for at least one mipmap directory
        $mipmapDirs = glob($dir.'/android/res/mipmap-*');
        if (empty($mipmapDirs)) {
            return [
                'valid' => false,
                'message' => 'Invalid Android icon structure. No mipmap directories found in android/res.',
            ];
        }

        // Validate web structure (check for favicon)
        if (! File::exists($dir.'/web/favicon.ico')) {
            return [
                'valid' => false,
                'message' => 'Invalid web icon structure. Missing web/favicon.ico file.',
            ];
        }

        return [
            'valid' => true,
            'message' => 'Icon structure is valid.',
        ];
    }

    /**
     * Recursively copy directory
     *
     * @param  string  $source  Source directory
     * @param  string  $destination  Destination directory
     * @return void
     */
    protected function copyDirectory($source, $destination)
    {
        File::ensureDirectoryExists($destination);

        $items = File::allFiles($source);

        foreach ($items as $item) {
            $relativePath = str_replace($source, '', $item->getPathname());
            $targetPath = $destination.$relativePath;

            File::ensureDirectoryExists(dirname($targetPath));
            File::copy($item->getPathname(), $targetPath);
        }
    }
}
