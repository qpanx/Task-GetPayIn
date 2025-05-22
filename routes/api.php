<?php

use App\Http\Controllers\API\ActivityLogController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PlatformController;
use App\Http\Controllers\API\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Posts routes
    Route::apiResource('posts', PostController::class);
    
    // Platforms routes
    Route::get('/platforms', [PlatformController::class, 'index']);
    Route::get('/user/platforms', [PlatformController::class, 'userPlatforms']);
    Route::post('/platforms/{id}/toggle', [PlatformController::class, 'togglePlatform']);
    Route::post('/platforms/{id}/credentials', [PlatformController::class, 'updateCredentials']);
    
    // Activity logs routes
    Route::get('/activity-logs', [ActivityLogController::class, 'index']);
    Route::get('/activity-logs/summary', [ActivityLogController::class, 'summary']);
}); 