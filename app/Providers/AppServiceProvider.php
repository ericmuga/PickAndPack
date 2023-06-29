<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        date_default_timezone_set('Africa/Nairobi');

        Inertia::share([
            'flash' => function () {
                return [
                    'message' => session('success'),
                    'type' => 'success',
                ];
            },
        ]);
    }
}
