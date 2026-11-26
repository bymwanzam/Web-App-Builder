<?php

namespace App\Http\Controllers;

use App\Models\AppBuild;
use Illuminate\Support\Facades\Storage;

class BuildController extends Controller
{
    /**
     * Public download build artifact using signed URL.
     */
    public function publicDownload(AppBuild $build)
    {
        // Validate signed URL
        if (! request()->hasValidSignature()) {
            abort(401, 'Invalid or expired download link.');
        }

        // Check build status
        if ($build->status !== 'completed') {
            abort(404, 'Build is not completed yet.');
        }

        // Check if artifact exists
        if (! $build->artifact_url) {
            abort(404, 'Build artifact not found.');
        }

        // Check if build has expired
        if ($build->expires_at && $build->expires_at->isPast()) {
            abort(410, 'Build artifact has expired and is no longer available.');
        }

        // Check if file exists in storage
        if (! Storage::disk('builds')->exists($build->artifact_url)) {
            abort(404, 'Build artifact file not found.');
        }

        // Load app relationship for filename
        $build->load('app');

        // Stream the file for download
        $extension = $build->getFileExtension();
        $filename = "{$build->app->name}-{$build->version_name}-{$build->build_type}.{$extension}";

        // Set correct MIME types to prevent browsers from adding .zip extension
        $mimeTypes = [
            'apk' => 'application/vnd.android.package-archive',
            'aab' => 'application/x-authorware-bin',
            'ipa' => 'application/octet-stream',
        ];

        $contentType = $mimeTypes[$extension] ?? 'application/octet-stream';

        return Storage::disk('builds')->download(
            $build->artifact_url,
            $filename,
            [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]
        );
    }
}
