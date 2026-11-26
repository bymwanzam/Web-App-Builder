<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $config = $this->getConfig();
        $latestBuild = $this->builds->first();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'platform' => $this->platformPlugin?->slug,
            'platform_name' => $this->platformPlugin?->name,
            'version_name' => $this->version_name,
            'version_code' => $this->version_code,
            'url' => $config?->url,
            'package_name' => $config?->package_name,
            'latest_build' => $latestBuild ? [
                'id' => $latestBuild->id,
                'status' => $latestBuild->status,
                'build_format' => $latestBuild->build_format,
                'version_name' => $latestBuild->version_name,
                'version_code' => $latestBuild->version_code,
                'created_at' => $latestBuild->created_at->toIso8601String(),
            ] : null,
            'has_active_build' => $this->hasActiveBuilds(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
