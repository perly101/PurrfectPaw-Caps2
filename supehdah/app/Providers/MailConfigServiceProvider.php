<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Comprehensive SSL configuration for Laravel's mail system
        $sslOptions = [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
            'security_level' => 0,
            'verify_depth' => 0,
            'disable_compression' => true,
            'SNI_enabled' => false,
            'ciphers' => 'DEFAULT:!DH'
        ];
        
        // Apply to SMTP mailer options
        Config::set('mail.mailers.smtp.options', $sslOptions);
        
        // Set transport settings for Swift Mailer
        Config::set('mail.mailers.smtp.stream_options', ['ssl' => $sslOptions]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}