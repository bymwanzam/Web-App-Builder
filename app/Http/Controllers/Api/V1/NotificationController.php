<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendNotificationRequest;
use App\Http\Resources\Api\NotificationResource;
use App\Jobs\SendPushNotificationJob;
use App\Models\App;
use App\Models\AppNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Notifications
 *
 * APIs for sending and managing push notifications to your app users.
 */
class NotificationController extends Controller
{
    /**
     * List notifications for an app
     *
     * Get a paginated list of push notifications sent to a specific app.
     *
     * @authenticated
     *
     * @queryParam per_page int Number of items per page. Default: 15. Example: 10
     *
     * @response 200 scenario="Success" {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Welcome!",
     *       "body": "Thanks for installing our app.",
     *       "status": "sent",
     *       "sent_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ],
     *   "meta": {
     *     "current_page": 1,
     *     "total": 10
     *   }
     * }
     */
    public function index(Request $request, App $app): AnonymousResourceCollection
    {
        $this->authorizeScope($request, 'notifications:read');
        $this->authorizeOwnership($request, $app);

        $notifications = AppNotification::where('app_id', $app->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return NotificationResource::collection($notifications);
    }

    /**
     * Send a push notification
     *
     * Send a push notification to all users of a specific app. Requires push notifications to be configured.
     *
     * @authenticated
     *
     * @bodyParam title string required The notification title. Example: New Update Available
     * @bodyParam body string required The notification body/message. Example: Check out our latest features!
     * @bodyParam image_url string URL to an image to include in the notification. Example: https://example.com/image.png
     * @bodyParam scheduled_at string Schedule the notification for a future time (ISO 8601 format). Example: 2024-01-15T10:00:00Z
     *
     * @response 201 scenario="Success" {
     *   "data": {
     *     "id": 1,
     *     "title": "New Update Available",
     *     "body": "Check out our latest features!",
     *     "status": "pending",
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     * @response 400 scenario="Notifications Not Configured" {
     *   "message": "Push notifications are not configured for this app.",
     *   "error": "notifications_not_configured"
     * }
     */
    public function store(SendNotificationRequest $request, App $app): NotificationResource|JsonResponse
    {
        $this->authorizeScope($request, 'notifications:send');
        $this->authorizeOwnership($request, $app);

        // Check if app has push notifications configured
        $pushConfig = $app->pushNotificationConfig;
        if (! $pushConfig || ! $pushConfig->enabled) {
            return response()->json([
                'message' => 'Push notifications are not configured for this app.',
                'error' => 'notifications_not_configured',
            ], 400);
        }

        $notification = AppNotification::create([
            'app_id' => $app->id,
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'body' => $request->body,
            'image_url' => $request->image_url,
            'notification_style' => $request->image_url ? 'image' : 'text',
            'status' => $request->scheduled_at ? 'scheduled' : 'pending',
            'scheduled_at' => $request->scheduled_at,
        ]);

        // If not scheduled, dispatch the job immediately
        if (! $request->scheduled_at) {
            SendPushNotificationJob::dispatch($notification);
        }

        return new NotificationResource($notification);
    }

    /**
     * Get notification details
     *
     * Get details for a specific notification including status and delivery information.
     *
     * @authenticated
     *
     * @response 200 scenario="Success" {
     *   "data": {
     *     "id": 1,
     *     "title": "Welcome!",
     *     "body": "Thanks for installing our app.",
     *     "status": "sent",
     *     "sent_at": "2024-01-01T00:00:00.000000Z",
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     * @response 404 scenario="Not Found" {
     *   "message": "Notification not found."
     * }
     */
    public function show(Request $request, AppNotification $notification): NotificationResource
    {
        $this->authorizeScope($request, 'notifications:read');

        // Verify ownership through the app
        if ($notification->app->user_id !== $request->user()->id) {
            abort(404, 'Notification not found.');
        }

        return new NotificationResource($notification);
    }

    /**
     * Cancel a scheduled notification
     *
     * Cancel a notification that has not been sent yet. Only works for pending or scheduled notifications.
     *
     * @authenticated
     *
     * @response 200 scenario="Success" {
     *   "message": "Notification cancelled successfully."
     * }
     * @response 400 scenario="Already Sent" {
     *   "message": "Cannot cancel a notification that has already been sent.",
     *   "error": "already_sent",
     *   "status": "sent"
     * }
     * @response 404 scenario="Not Found" {
     *   "message": "Notification not found."
     * }
     */
    public function destroy(Request $request, AppNotification $notification): JsonResponse
    {
        $this->authorizeScope($request, 'notifications:send');

        // Verify ownership through the app
        if ($notification->app->user_id !== $request->user()->id) {
            abort(404, 'Notification not found.');
        }

        // Can only cancel pending or scheduled notifications
        if (! in_array($notification->status, ['pending', 'scheduled'])) {
            return response()->json([
                'message' => 'Cannot cancel a notification that has already been sent.',
                'error' => 'already_sent',
                'status' => $notification->status,
            ], 400);
        }

        $notification->delete();

        return response()->json([
            'message' => 'Notification cancelled successfully.',
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
