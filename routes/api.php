<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Cms\AnnouncementController as CmsAnnouncementController;
use App\Http\Controllers\Api\V1\Cms\DashboardController as CmsDashboardController;
use App\Http\Controllers\Api\V1\Cms\FileController as CmsFileController;
use App\Http\Controllers\Api\V1\Cms\InformationController as CmsInformationController;
use App\Http\Controllers\Api\V1\Cms\RegistrationFlowController as CmsRegistrationFlowController;
use App\Http\Controllers\Api\V1\Cms\TimelineController as CmsTimelineController;
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

    // CMS Dashboard API Endpoints
    Route::prefix('cms/dashboard')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [CmsDashboardController::class, 'index']);
        Route::get('stats', [CmsDashboardController::class, 'stats']);
        Route::get('drafts', [CmsDashboardController::class, 'drafts']);
        Route::get('activity', [CmsDashboardController::class, 'activity']);
        Route::get('analytics', [CmsDashboardController::class, 'analytics']);
        Route::get('system-health', [CmsDashboardController::class, 'systemHealth']);
    });

    // CMS Management API Endpoints (Backend-only for SPA/React consumption)
    Route::prefix('cms')->middleware('auth:sanctum')->group(function () {
        Route::apiResource('information', CmsInformationController::class);
        Route::apiResource('announcements', CmsAnnouncementController::class);
        Route::apiResource('timelines', CmsTimelineController::class);
        Route::apiResource('files', CmsFileController::class);
        Route::apiResource('registration-steps', CmsRegistrationFlowController::class);
    });

});
