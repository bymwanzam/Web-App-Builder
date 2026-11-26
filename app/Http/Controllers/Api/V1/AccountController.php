<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\AccountResource;
use App\Http\Resources\Api\SubscriptionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Account
 *
 * APIs for retrieving account information, subscription details, and usage statistics.
 */
class AccountController extends Controller
{
    /**
     * Get account information
     *
     * Retrieve the authenticated user's account details including name, email, and plan information.
     *
     * @authenticated
     *
     * @response 200 scenario="Success" {
     *   "data": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "plan": {
     *       "id": 1,
     *       "name": "Pro"
     *     },
     *     "build_credits": 50,
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     */
    public function show(Request $request): AccountResource
    {
        $this->authorizeScope($request, 'account:read');

        $user = $request->user();
        $user->load('plan');

        return new AccountResource($user);
    }

    /**
     * Get subscription information
     *
     * Retrieve the authenticated user's current subscription details including plan, status, and renewal date.
     *
     * @authenticated
     *
     * @response 200 scenario="Active subscription" {
     *   "data": {
     *     "id": 1,
     *     "status": "active",
     *     "amount": 29.99,
     *     "payment_method": "stripe",
     *     "plan": {
     *       "id": 2,
     *       "name": "Pro",
     *       "price": 29.99
     *     },
     *     "renewal_at": "2024-02-01T00:00:00.000000Z"
     *   }
     * }
     * @response 200 scenario="No subscription" {
     *   "message": "No active subscription found.",
     *   "plan": {
     *     "id": 1,
     *     "name": "Free",
     *     "price": 0
     *   }
     * }
     */
    public function subscription(Request $request): SubscriptionResource|JsonResponse
    {
        $this->authorizeScope($request, 'account:read');

        $user = $request->user();
        $subscription = $user->activeSubscription;

        if (! $subscription) {
            return response()->json([
                'message' => 'No active subscription found.',
                'plan' => [
                    'id' => $user->plan?->id,
                    'name' => $user->plan?->name ?? 'Free',
                    'price' => $user->plan?->price ?? 0,
                ],
            ]);
        }

        $subscription->load('plan');

        return new SubscriptionResource($subscription);
    }

    /**
     * Get usage statistics
     *
     * Retrieve the authenticated user's usage statistics including build credits, app counts, and build history.
     *
     * @authenticated
     *
     * @response 200 scenario="Success" {
     *   "build_credits": {
     *     "available": 45,
     *     "monthly_allocation": 100,
     *     "unlimited": false
     *   },
     *   "apps": {
     *     "total": 5
     *   },
     *   "builds": {
     *     "total": 23,
     *     "completed": 20,
     *     "failed": 3
     *   },
     *   "plan": {
     *     "id": 2,
     *     "name": "Pro",
     *     "features": ["Unlimited apps", "Priority support"]
     *   }
     * }
     */
    public function usage(Request $request): JsonResponse
    {
        $this->authorizeScope($request, 'account:read');

        $user = $request->user();
        $user->load('plan');

        $totalApps = $user->apps()->count();
        $totalBuilds = $user->apps()->withCount('builds')->get()->sum('builds_count');
        $completedBuilds = \App\Models\AppBuild::whereHas('app', fn ($q) => $q->where('user_id', $user->id))
            ->where('status', 'completed')
            ->count();
        $failedBuilds = \App\Models\AppBuild::whereHas('app', fn ($q) => $q->where('user_id', $user->id))
            ->where('status', 'failed')
            ->count();

        $plan = $user->plan;
        $monthlyCredits = $plan?->monthly_build_credits ?? 0;
        $isUnlimited = $monthlyCredits === -1;

        return response()->json([
            'build_credits' => [
                'available' => $isUnlimited ? 'unlimited' : $user->build_credits,
                'monthly_allocation' => $isUnlimited ? 'unlimited' : $monthlyCredits,
                'unlimited' => $isUnlimited,
            ],
            'apps' => [
                'total' => $totalApps,
            ],
            'builds' => [
                'total' => $totalBuilds,
                'completed' => $completedBuilds,
                'failed' => $failedBuilds,
            ],
            'plan' => [
                'id' => $plan?->id,
                'name' => $plan?->name ?? 'Free',
                'features' => $plan?->features ?? [],
            ],
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
}
