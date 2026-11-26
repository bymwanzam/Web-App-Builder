<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateAppRequest;
use App\Http\Requests\Api\UpdateAppRequest;
use App\Http\Resources\Api\AppResource;
use App\Models\App;
use App\Models\Plugin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Apps
 *
 * APIs for managing your mobile applications.
 */
class AppController extends Controller
{
    /**
     * List all apps
     *
     * Get a paginated list of all apps belonging to the authenticated user.
     *
     * @authenticated
     *
     * @queryParam per_page int Number of items per page. Default: 15. Example: 10
     *
     * @response 200 scenario="Success" {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "My App",
     *       "platform": "android-webview",
     *       "version_name": "1.0.0",
     *       "version_code": 1
     *     }
     *   ],
     *   "meta": {
     *     "current_page": 1,
     *     "total": 5
     *   }
     * }
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorizeScope($request, 'apps:read');

        $apps = $request->user()
            ->apps()
            ->with(['platformPlugin', 'builds' => fn ($q) => $q->latest()->limit(1)])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return AppResource::collection($apps);
    }

    /**
     * Create a new app
     *
     * Create a new app for the authenticated user.
     *
     * @authenticated
     *
     * @bodyParam name string required The name of the app. Example: My App
     * @bodyParam platform string required The platform slug (e.g., android-webview). Example: android-webview
     * @bodyParam url string required The website URL for the app. Example: https://example.com
     * @bodyParam package_name string required The package name (e.g., com.example.app). Example: com.example.myapp
     * @bodyParam version_name string The initial version name. Default: 1.0.0. Example: 1.0.0
     * @bodyParam version_code int The initial version code. Default: 1. Example: 1
     *
     * @response 201 scenario="Success" {
     *   "data": {
     *     "id": 1,
     *     "name": "My App",
     *     "platform": "android-webview",
     *     "version_name": "1.0.0",
     *     "version_code": 1,
     *     "url": "https://example.com",
     *     "package_name": "com.example.myapp"
     *   }
     * }
     * @response 422 scenario="Validation Error" {
     *   "message": "The name field is required.",
     *   "errors": {"name": ["The name field is required."]}
     * }
     */
    public function store(CreateAppRequest $request): AppResource
    {
        $this->authorizeScope($request, 'apps:write');

        $platformPlugin = Plugin::where('slug', $request->platform)
            ->where('type', 'platform')
            ->where('status', 'active')
            ->firstOrFail();

        $app = App::create([
            'user_id' => $request->user()->id,
            'platform_plugin_id' => $platformPlugin->id,
            'name' => $request->name,
            'version_name' => $request->version_name ?? '1.0.0',
            'version_code' => $request->version_code ?? 1,
        ]);

        // Create platform-specific configuration
        $platform = app(\App\Services\PluginManager::class)->getPlatformById($platformPlugin->id);
        if ($platform) {
            $configModelClass = $platform->getConfigModelClass();
            $configData = array_merge(
                ['app_id' => $app->id],
                $request->only(['url', 'package_name'])
            );
            $configModelClass::create($configData);
        }

        $app->load(['platformPlugin', 'builds']);

        return new AppResource($app);
    }

    /**
     * Get app details
     *
     * Get details for a specific app including recent builds.
     *
     * @authenticated
     *
     * @response 200 scenario="Success" {
     *   "data": {
     *     "id": 1,
     *     "name": "My App",
     *     "platform": "android-webview",
     *     "version_name": "1.0.0",
     *     "version_code": 1,
     *     "builds": []
     *   }
     * }
     * @response 404 scenario="Not Found" {
     *   "message": "App not found."
     * }
     */
    public function show(Request $request, App $app): AppResource
    {
        $this->authorizeScope($request, 'apps:read');
        $this->authorizeOwnership($request, $app);

        $app->load(['platformPlugin', 'builds' => fn ($q) => $q->latest()->limit(5)]);

        return new AppResource($app);
    }

    /**
     * Update an app
     *
     * Update an existing app's details and configuration.
     *
     * @authenticated
     *
     * @bodyParam name string The app name. Example: Updated App Name
     * @bodyParam version_name string The version name. Example: 1.0.1
     * @bodyParam version_code int The version code. Example: 2
     * @bodyParam url string The website URL. Example: https://newsite.com
     * @bodyParam package_name string The package name. Example: com.example.newapp
     *
     * @response 200 scenario="Success" {
     *   "data": {
     *     "id": 1,
     *     "name": "Updated App Name",
     *     "platform": "android-webview",
     *     "version_name": "1.0.1",
     *     "version_code": 2
     *   }
     * }
     * @response 404 scenario="Not Found" {
     *   "message": "App not found."
     * }
     */
    public function update(UpdateAppRequest $request, App $app): AppResource
    {
        $this->authorizeScope($request, 'apps:write');
        $this->authorizeOwnership($request, $app);

        $app->update($request->only(['name', 'version_name', 'version_code']));

        // Update platform-specific configuration if provided
        $config = $app->getConfig();
        if ($config) {
            $configFields = $request->only(['url', 'package_name']);
            if (! empty($configFields)) {
                $config->update($configFields);
            }
        }

        $app->load(['platformPlugin', 'builds']);

        return new AppResource($app);
    }

    /**
     * Delete an app
     *
     * Delete an app and all its associated data including builds and configurations.
     *
     * @authenticated
     *
     * @response 200 scenario="Success" {
     *   "message": "App deleted successfully."
     * }
     * @response 404 scenario="Not Found" {
     *   "message": "App not found."
     * }
     * @response 409 scenario="Has Active Builds" {
     *   "message": "Cannot delete app with active builds.",
     *   "error": "active_builds"
     * }
     */
    public function destroy(Request $request, App $app): JsonResponse
    {
        $this->authorizeScope($request, 'apps:write');
        $this->authorizeOwnership($request, $app);

        // Check if app has active builds
        if ($app->hasActiveBuilds()) {
            return response()->json([
                'message' => 'Cannot delete app with active builds.',
                'error' => 'active_builds',
            ], 409);
        }

        $app->delete();

        return response()->json([
            'message' => 'App deleted successfully.',
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
     * Authorize that the user owns the resource.
     */
    protected function authorizeOwnership(Request $request, App $app): void
    {
        if ($app->user_id !== $request->user()->id) {
            abort(404, 'App not found.');
        }
    }
}
