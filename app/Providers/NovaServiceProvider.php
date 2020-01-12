<?php

namespace App\Providers;

use App\Http\Controllers\SocialLoginController;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes();

        Route::domain(config('nova.domain', null))
            ->middleware(['web'])
            ->as('nova.')
            ->prefix(Nova::path())
            ->group(function () {
                Route::get('/login', 'Laravel\Nova\Http\Controllers\LoginController@showLoginForm');
                Route::get('/login/google', SocialLoginController::class . '@redirectToGoogle')
                    ->name('login.google');
                Route::get('/google/callback', SocialLoginController::class . '@processGoogleCallback')
                    ->name('login.callback');
            });

        Route::namespace('Laravel\Nova\Http\Controllers')
            ->domain(config('nova.domain', null))
            ->middleware(config('nova.middleware', []))
            ->as('nova.')
            ->prefix(Nova::path())
            ->group(function () {
                Route::get('/logout', 'LoginController@logout')->name('logout');
            });
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            new Help,
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
