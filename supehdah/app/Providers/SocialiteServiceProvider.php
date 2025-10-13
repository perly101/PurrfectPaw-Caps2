<?php
namespace App\Providers;

use App\Support\Socialite\Socialite;
use Illuminate\Support\ServiceProvider;

class SocialiteServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('socialite', function ($app) {
            return new Socialite();
        });
    }

    public function boot()
    {
        // Nothing to do here
    }
}