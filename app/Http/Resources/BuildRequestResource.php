<?php

namespace App\Http\Resources;

use App\DataTransferObjects\BuildConfigContext;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuildRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * This resource delegates the actual configuration generation to the
     * platform plugin, ensuring clean separation of concerns and extensibility.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $app = $this->app;

        // Get the platform plugin instance
        $platform = $app->getPlatformInstance();

        if (! $platform) {
            throw new \Exception("No platform plugin found for app {$app->id}");
        }

        // Create build context and delegate to platform plugin
        $context = new BuildConfigContext($app, $this->resource);

        return [
            'id' => (string) $this->id,
            'build_type' => $this->build_type,
            'platform' => $this->platform,
            'created_at' => $this->created_at->toIso8601String(),
            'config' => $platform->prepareBuildConfig($context),
        ];
    }
}
