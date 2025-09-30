<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Swift_Preferences;

class SwiftMailerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Comprehensive fix for SSL certificate verification issues
        $streamOptions = [
            'ssl' => [
                // Disable certificate verification completely
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
                // Additional options to try and fix SSL issues
                'security_level' => 0,
                'verify_depth' => 0,
                'disable_compression' => true,
                'SNI_enabled' => false,
                'ciphers' => 'DEFAULT:!DH'
            ]
        ];
        
        // Apply these settings globally to all SSL connections
        stream_context_set_default($streamOptions);
        
        // Set php.ini settings for SSL
        ini_set('openssl.cafile', '');
        ini_set('openssl.capath', '');
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