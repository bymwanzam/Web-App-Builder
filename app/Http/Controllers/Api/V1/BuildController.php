<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TriggerBuildRequest;
use App\Http\Resources\Api\BuildResource;
use App\Models\App;
use App\Models\AppBuild;
use App\Models\AppBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Builds
 *
 * APIs for managing app builds. Trigger new builds, check status, and download artifacts.
 */
class BuildController extends Controller
{
    /**
     * List builds for an app
     *
     * Get a paginated list of builds for a specific app.
     *
     * @authenticated
     *
     * @queryParam per_page int Number of items per page. Default: 15. Example: 10
     *
     * @response 200 scenario="Success" {
     *   "data": [
     *     {
     *       "id": 1,
     *       "status": "completed",
     *       "build_type": "debug",
     *       "build_format": "apk",
     *       "version_name": "1.0.0",
     *       "version_code": 1,
     *       "created_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ],
     *   "meta": {
     *     "current_page": 1,
     *     "total": 5
     *   }
     * }
     */
    public function index(Request $request, App $app): AnonymousResourceCollection
    {
        $this->authorizeScope($request, 'builds:read');
        $this->authorizeOwnership($request, $app);

        $builds = $app->builds()
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return BuildResource::collection($builds);
    }

    /**
     * Trigger a new build
     *
     * Trigger a new build for a specific app. Requires available build credits.
     *
     * @authenticated
     *
     * @bodyParam build_type string The build type: debug or release. Default: debug. Example: debug
     * @bodyParam build_format string The build format: apk or aab. Default: apk. Example: apk
     * @bodyParam keystore_id int The keystore ID for release builds. Example: 1
     *
     * @response 201 scenario="Success" {
     *   "data": {
     *     "id": 1,
     *     "status": "pending",
     *     "build_type": "debug",
     *     "build_format": "apk",
     *     "version_name": "1.0.1",
     *     "version_code": 2
     *   }
     * }
     * @response 402 scenario="Insufficient Credits" {
     *   "message": "Insufficient build credits.",
     *   "error": "insufficient_credits",
     *   "required_credits": 1,
     *   "available_credits": 0
     * }
     * @response 409 scenario="Active Build" {
     *   "message": "App already has an active build in progress.",
     *   "error": "active_build"
     * }
     * @response 503 scenario="No Builders Available" {
     *   "message": "No builders available. Please try again later.",
     *   "error": "no_builders"
     * }
     */
    public function store(TriggerBuildRequest $request, App $app): BuildResource|JsonResponse
    {
        $this->authorizeScope($request, 'builds:trigger');
        $this->authorizeOwnership($request, $app);

        $user = $request->user();

        // Get an available builder
        $platform = $app->getPlatformInstance();
        if (! $platform) {
            return response()->json([
                'message' => 'Platform not configured for this app.',
                'error' => 'invalid_platform',
            ], 400);
        }

        // Check allowed builders for user's plan
        $allowedBuilderIds = $user->plan?->allowed_builders ?? [];
        $builder = AppBuilder::where('status', 'active')
            ->when(! empty($allowedBuilderIds), fn ($q) => $q->whereIn('id', $allowedBuilderIds))
            ->whereColumn('current_builds_count', '<', 'max_queue')
            ->orderBy('current_builds_count')
            ->first();

        if (! $builder) {
            return response()->json([
                'message' => 'No builders available. Please try again later.',
                'error' => 'no_builders',
            ], 503);
        }

        // Check build credits
        $creditCost = $builder->credit_cost ?? 1;
        if (! $user->hasBuildCredits('android', $creditCost)) {
            return response()->json([
                'message' => 'Insufficient build credits.',
                'error' => 'insufficient_credits',
                'required_credits' => $creditCost,
                'available_credits' => $user->build_credits,
            ], 402);
        }

        // Check for active builds
        if ($app->hasActiveBuilds()) {
            return response()->json([
                'message' => 'App already has an active build in progress.',
                'error' => 'active_build',
            ], 409);
        }

        // Increment version
        $versionCode = $app->incrementVersionCode();
        $versionName = $app->incrementVersionName('android');
        $app->update(['version_name' => $versionName]);

        // Create the build
        $build = AppBuild::create([
            'app_id' => $app->id,
            'app_builder_id' => $builder->id,
            'platform' => $platform->getPlatformId(),
            'build_type' => $request->build_type ?? 'debug',
            'build_format' => $request->build_format ?? 'apk',
            'version_name' => $versionName,
            'version_code' => $versionCode,
            'status' => 'pending',
            'keystore_id' => $request->keystore_id,
        ]);

        // Deduct credits
        $user->deductBuildCredits('android', $creditCost);

        // Increment builder queue count
        $builder->increment('current_builds_count');

        return new BuildResource($build);
    }

    /**
     * Get build details
     *
     * Get details for a specific build including status and logs.
     *
     * @authenticated
     *
     * @response 200 scenario="Success" {
     *   "data": {
     *     "id": 1,
     *     "status": "completed",
     *     "build_type": "debug",
     *     "build_format": "apk",
     *     "version_name": "1.0.0",
     *     "version_code": 1,
     *     "artifact_size": 15000000,
     *     "build_duration": 120,
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "completed_at": "2024-01-01T00:02:00.000000Z"
     *   }
     * }
     * @response 404 scenario="Not Found" {
     *   "message": "Build not found."
     * }
     */
    public function show(Request $request, AppBuild $build): BuildResource
    {
        $this->authorizeScope($request, 'builds:read');

        // Verify ownership through the app
        if ($build->app->user_id !== $request->user()->id) {
            abort(404, 'Build not found.');
        }

        return new BuildResource($build);
    }

    /**
     * Download build artifact
     *
     * Get the download URL for a completed build artifact.
     *
     * @authenticated
     *
     * @response 200 scenario="Success" {
     *   "download_url": "https://example.com/builds/app.apk?token=abc123",
     *   "file_name": "My App.apk",
     *   "file_size": 15000000,
     *   "expires_at": "2024-01-08T00:00:00+00:00"
     * }
     * @response 400 scenario="Build Not Completed" {
     *   "message": "Build is not completed yet.",
     *   "error": "build_not_completed",
     *   "status": "building"
     * }
     * @response 404 scenario="Artifact Not Found" {
     *   "message": "Build artifact not available.",
     *   "error": "artifact_not_found"
     * }
     */
    public function download(Request $request, AppBuild $build): JsonResponse
    {
        $this->authorizeScope($request, 'builds:download');

        // Verify ownership through the app
        if ($build->app->user_id !== $request->user()->id) {
            abort(404, 'Build not found.');
        }

        if ($build->status !== 'completed') {
            return response()->json([
                'message' => 'Build is not completed yet.',
                'error' => 'build_not_completed',
                'status' => $build->status,
            ], 400);
        }

        if (! $build->artifact_url) {
            return response()->json([
                'message' => 'Build artifact not available.',
                'error' => 'artifact_not_found',
            ], 404);
        }

        // Return the download URL
        return response()->json([
            'download_url' => $build->download_url,
            'file_name' => $build->app->name.'.'.$build->getFileExtension(),
            'file_size' => $build->artifact_size,
            'expires_at' => $build->expires_at?->toIso8601String(),
        ]);
    }

    /**
     * Get build logs
     *
     * Get the build logs for a specific build. Useful for debugging failed builds.
     *
     * @authenticated
     *
     * @response 200 scenario="Success" {
     *   "build_id": 1,
     *   "status": "completed",
     *   "logs": "Build started...\nCompiling...\nBuild completed successfully.",
     *   "error_message": null
     * }
     * @response 200 scenario="Failed Build" {
     *   "build_id": 2,
     *   "status": "failed",
     *   "logs": "Build started...\nCompiling...",
     *   "error_message": "Compilation failed: missing dependency"
     * }
     */
    public function logs(Request $request, AppBuild $build): JsonResponse
    {
        $this->authorizeScope($request, 'builds:read');

        // Verify ownership through the app
        if ($build->app->user_id !== $request->user()->id) {
            abort(404, 'Build not found.');
        }

        return response()->json([
            'build_id' => $build->id,
            'status' => $build->status,
            'logs' => $build->build_logs,
            'error_message' => $build->error_message,
        ]);
    }

    /**
     * Authorize that the token has the required scope.
     */
    protected function authorizeScope(Request $request, string $scope): void
    {
        $token = $request->user()->currentAccessToken();

        if (! $token->can($scope) && ! $token->can('*')) {
            abort(403, "Token does not have the required scope: {$scope}");
        }
    }

    /**
     * Authorize that the user owns the app.
     */
    protected function authorizeOwnership(Request $request, App $app): void
    {
        if ($app->user_id !== $request->user()->id) {
            abort(404, 'App not found.');
        }
    }
}
