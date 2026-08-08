<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PublicAnnouncementController;
use App\Http\Controllers\Api\V1\PublicFileController;
use App\Http\Controllers\Api\V1\PublicInformationController;
use App\Http\Controllers\Api\V1\PublicRegistrationFlowController;
use App\Http\Controllers\Api\V1\PublicTimelineController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - V1
|--------------------------------------------------------------------------
|
| All routes in this file are prefixed with 'v1' and assigned the 'api'
| middleware group.
|
*/

Route::prefix('v1')->middleware('throttle:60,1')->group(function () {

    // Authentication Routes
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
        });
    });

    // Public Content Endpoints
    Route::get('information', [PublicInformationController::class, 'index']);
    Route::get('information/{slug}', [PublicInformationController::class, 'show']);

    Route::get('announcements', [PublicAnnouncementController::class, 'index']);
    Route::get('announcements/{slug}', [PublicAnnouncementController::class, 'show']);

    Route::get('timelines', [PublicTimelineController::class, 'index']);
    Route::get('timelines/{id}', [PublicTimelineController::class, 'show']);

    Route::get('files', [PublicFileController::class, 'index']);
    Route::get('files/{slug}', [PublicFileController::class, 'show']);

    Route::get('registration-flow', [PublicRegistrationFlowController::class, 'index']);
    Route::get('registration-flow/{slug}', [PublicRegistrationFlowController::class, 'show']);

});
