<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureApiAccess;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EnsureApiAccessMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_allows_access_when_user_plan_has_api_access()
    {
        $plan = Plan::factory()->create([
            'can_use_api' => true,
        ]);

        $user = User::factory()->create([
            'plan_id' => $plan->id,
        ]);

        $middleware = new EnsureApiAccess;
        $request = Request::create('/api/v1/account', 'GET');
        $request->setUserResolver(fn () => $user);

        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    #[Test]
    public function it_denies_access_when_user_plan_does_not_have_api_access()
    {
        $plan = Plan::factory()->create([
            'can_use_api' => false,
        ]);

        $user = User::factory()->create([
            'plan_id' => $plan->id,
        ]);

        $middleware = new EnsureApiAccess;
        $request = Request::create('/api/v1/account', 'GET');
        $request->setUserResolver(fn () => $user);

        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('api_access_denied', $response->getContent());
    }

    #[Test]
    public function it_denies_access_when_user_has_no_plan()
    {
        $user = User::factory()->create([
            'plan_id' => null,
        ]);

        $middleware = new EnsureApiAccess;
        $request = Request::create('/api/v1/account', 'GET');
        $request->setUserResolver(fn () => $user);

        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(403, $response->getStatusCode());
    }

    #[Test]
    public function it_denies_access_when_no_user_authenticated()
    {
        $middleware = new EnsureApiAccess;
        $request = Request::create('/api/v1/account', 'GET');
        $request->setUserResolver(fn () => null);

        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(403, $response->getStatusCode());
    }

    #[Test]
    public function it_returns_json_error_response()
    {
        $plan = Plan::factory()->create([
            'can_use_api' => false,
        ]);

        $user = User::factory()->create([
            'plan_id' => $plan->id,
        ]);

        $middleware = new EnsureApiAccess;
        $request = Request::create('/api/v1/account', 'GET');
        $request->setUserResolver(fn () => $user);

        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('api_access_denied', $data['error']);
        $this->assertStringContainsString('not available', $data['message']);
    }

    #[Test]
    public function it_integrates_with_api_routes()
    {
        $this->seed(\Database\Seeders\PlatformPluginsSeeder::class);

        $planWithApi = Plan::factory()->create(['can_use_api' => true]);
        $planWithoutApi = Plan::factory()->create(['can_use_api' => false]);

        $userWithApi = User::factory()->create(['plan_id' => $planWithApi->id]);
        $userWithoutApi = User::factory()->create(['plan_id' => $planWithoutApi->id]);

        // User with API access
        Sanctum::actingAs($userWithApi, ['*']);
        $this->getJson('/api/v1/account')->assertOk();

        // User without API access
        Sanctum::actingAs($userWithoutApi, ['*']);
        $this->getJson('/api/v1/account')->assertForbidden();
    }
}
