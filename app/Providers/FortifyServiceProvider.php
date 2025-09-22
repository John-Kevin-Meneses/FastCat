<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind CreateNewUser action
        $this->app->singleton(CreatesNewUsers::class, CreateNewUser::class);
    }

    public function boot(): void
    {
        // 🚫 Disable Fortify’s default /register, /login, /logout (web routes)
        Fortify::ignoreRoutes();

        // ✅ Register API-only auth routes
        Route::group([
            'middleware' => ['api'],
            'prefix' => 'api',
        ], function () {
            Route::post('/register', [RegisteredUserController::class, 'store']);
            Route::post('/login', [AuthenticatedSessionController::class, 'store']);
            Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
        });

        // === Fortify actions ===
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        // === Rate limiting ===
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::lower($request->input(Fortify::username())) . '|' . $request->ip();
            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        // === Custom JSON responses ===
        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return response()->json([
                        'message' => 'User registered successfully',
                        'user' => $request->user(),
                    ], 201);
                }
            };
        });

        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    return response()->json([
                        'message' => 'Login successful',
                        'user' => $request->user(),
                    ]);
                }
            };
        });

        $this->app->singleton(LogoutResponse::class, function () {
            return new class implements LogoutResponse {
                public function toResponse($request)
                {
                    return response()->json([
                        'message' => 'Logout successful',
                    ]);
                }
            };
        });
    }
}
