<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\ScheduleController;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/

// ✅ Health check
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

/*
|--------------------------------------------------------------------------
| Protected API Routes (Requires Sanctum Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // ✅ Authenticated user info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ✅ Logout (revoke token)
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | General Authenticated Access (any logged-in user)
    |--------------------------------------------------------------------------
    */
    Route::get('/routes', [RouteController::class, 'index']);
    Route::get('/routes/{id}', [RouteController::class, 'show']);
    Route::get('/schedules', [ScheduleController::class, 'index']);
    Route::get('/schedules/{id}', [ScheduleController::class, 'show']);

    /*
    |--------------------------------------------------------------------------
    | Admin/Manager Access (role: super-admin OR manager)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:super-admin,manager')->group(function () {
        // Routes
        Route::post('/routes', [RouteController::class, 'store']);
        Route::patch('/routes/{id}', [RouteController::class, 'update']);
        Route::delete('/routes/{id}', [RouteController::class, 'destroy']);

        // Schedules
        Route::post('/schedules', [ScheduleController::class, 'store']);
        Route::patch('/schedules/{id}', [ScheduleController::class, 'update']);
        Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Super Admin Access Only
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:super-admin')->group(function () {
        Route::post('/admin/users/{user}/role', [UserManagementController::class, 'updateRole']);
    });
});
