<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register our custom SocialiteServiceProvider
        $this->app->register(\App\Providers\SocialiteServiceProvider::class);
        
        // Register our NotificationServiceProvider
        $this->app->register(\App\Providers\NotificationServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
