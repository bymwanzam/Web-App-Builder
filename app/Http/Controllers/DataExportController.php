<?php

namespace App\Http\Controllers;

use App\Models\DataExportRequest;
use Illuminate\Support\Facades\Storage;

class DataExportController extends Controller
{
    /**
     * Download data export using token.
     */
    public function download(string $token)
    {
        $request = DataExportRequest::where('download_token', $token)->first();

        if (! $request) {
            abort(404, 'Export not found.');
        }

        // Check if export is completed
        if ($request->status !== 'completed') {
            abort(404, 'Export is not ready yet.');
        }

        // Check if export has expired
        if ($request->isExpired()) {
            abort(410, 'Export has expired and is no longer available.');
        }

        // Check if file exists
        if (! $request->file_path || ! Storage::disk('local')->exists($request->file_path)) {
            abort(404, 'Export file not found.');
        }

        // Load user relationship for filename
        $request->load('user');

        // Stream the file for download
        $filename = 'data-export-'.($request->user->name ?? 'user').'-'.now()->format('Y-m-d').'.zip';
        $filename = preg_replace('/[^a-zA-Z0-9\-_\.]/', '-', $filename);

        return Storage::disk('local')->download(
            $request->file_path,
            $filename,
            [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]
        );
    }
}
