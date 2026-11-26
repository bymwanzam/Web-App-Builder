<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuildResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'app_id' => $this->app_id,
            'platform' => $this->platform,
            'build_type' => $this->build_type,
            'build_format' => $this->build_format,
            'version_name' => $this->version_name,
            'version_code' => $this->version_code,
            'status' => $this->status,
            'status_text' => $this->status_text,
            'artifact_size' => $this->artifact_size,
            'formatted_size' => $this->formatted_size,
            'build_duration' => $this->build_duration,
            'formatted_duration' => $this->formatted_duration,
            'download_url' => $this->when($this->status === 'completed' && $this->artifact_url, $this->download_url),
            'has_appetize_preview' => $this->hasAppetizePreview(),
            'appetize_preview_url' => $this->when($this->hasAppetizePreview(), $this->appetize_preview_url),
            'error_message' => $this->when($this->status === 'failed', $this->error_message),
            'started_at' => $this->started_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'expires_at' => $this->expires_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
