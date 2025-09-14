<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserManagementController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

// ✅ Admin-only route for assigning roles
Route::middleware(['auth:sanctum', 'role:super-admin'])->group(function () {
    Route::post('/admin/users/{user}/role', [UserManagementController::class, 'updateRole']);
});

// ✅ Registration route (Fortify controller)
Route::post('/register', [RegisteredUserController::class, 'store']);

// ✅ Authenticated user details
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ✅ Health check route
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});
