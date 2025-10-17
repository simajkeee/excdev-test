<?php

namespace App\Providers;

use App\Helpers\UrlHelper;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RedirectIfAuthenticated::redirectUsing(
            fn ($request) => UrlHelper::getUserUrl($request)
        );

        Inertia::share('auth', function () {
            return [
                'user' => Auth::user()
                    ? [
                        'id' => Auth::user()->id,
                        'role' => Auth::user()->role,
                    ]
                    : null,
            ];
        });
    }
}
