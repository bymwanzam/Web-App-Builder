<?php

use App\Http\Controllers\Api\BuildApiController;
use App\Http\Controllers\Api\V1\AccountController;
use App\Http\Controllers\Api\V1\AppController;
use App\Http\Controllers\Api\V1\BuildController;
use App\Http\Controllers\Api\V1\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Builder API Routes (Internal)
Route::middleware('builder.auth')->prefix('v1')->group(function () {
    Route::get('/builds/pending/{server_key}', [BuildApiController::class, 'pending']);
    Route::post('/builds/{build}/status/{server_key}', [BuildApiController::class, 'status']);
    Route::get('/keystores/{keystoreId}/download/{server_key}', [BuildApiController::class, 'downloadKeystore']);
});

/*
|--------------------------------------------------------------------------
| User API Routes (v1)
|--------------------------------------------------------------------------
|
| Public API endpoints for users with API access enabled on their plan.
| Authentication: Bearer token (Sanctum)
| Rate Limit: 60 requests per minute
|
*/
Route::middleware(['auth:sanctum', 'api.access', 'throttle:api'])
    ->prefix('v1')
    ->group(function () {
        // Apps
        Route::apiResource('apps', AppController::class);

        // Builds
        Route::get('apps/{app}/builds', [BuildController::class, 'index']);
        Route::post('apps/{app}/builds', [BuildController::class, 'store']);
        Route::get('builds/{build}', [BuildController::class, 'show']);
        Route::get('builds/{build}/download', [BuildController::class, 'download']);
        Route::get('builds/{build}/logs', [BuildController::class, 'logs']);

        // Notifications
        Route::get('apps/{app}/notifications', [NotificationController::class, 'index']);
        Route::post('apps/{app}/notifications', [NotificationController::class, 'store']);
        Route::get('notifications/{notification}', [NotificationController::class, 'show']);
        Route::delete('notifications/{notification}', [NotificationController::class, 'destroy']);

        // Account
        Route::get('account', [AccountController::class, 'show']);
        Route::get('account/subscription', [AccountController::class, 'subscription']);
        Route::get('account/usage', [AccountController::class, 'usage']);
    });
