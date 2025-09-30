<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            // Only load settings after database is ready (after migrations)
            if ($this->app->runningInConsole() && $this->app->runningUnitTests()) {
                return;
            }
            
            // Load email settings from database
            $this->loadEmailSettings();
            
            // You can add more settings groups here in the future
        } catch (\Exception $e) {
            // Log any errors but don't crash the app
            Log::error('Error loading settings: ' . $e->getMessage());
        }
    }

    /**
     * Load email settings from database into Laravel config
     */
    protected function loadEmailSettings()
    {
        // Get all email settings
        $emailSettings = Setting::getGroup('email');
        
        if (empty($emailSettings)) {
            return;
        }
        
        // Map database settings to Laravel config
        $configMappings = [
            'mail_driver' => 'mail.default',
            'mail_host' => 'mail.mailers.smtp.host',
            'mail_port' => 'mail.mailers.smtp.port',
            'mail_username' => 'mail.mailers.smtp.username',
            'mail_password' => 'mail.mailers.smtp.password',
            'mail_encryption' => 'mail.mailers.smtp.encryption',
            'mail_from_address' => 'mail.from.address',
            'mail_from_name' => 'mail.from.name',
        ];
        
        foreach ($configMappings as $dbKey => $configKey) {
            if (isset($emailSettings[$dbKey]) && !empty($emailSettings[$dbKey])) {
                Config::set($configKey, $emailSettings[$dbKey]);
                
                // For debugging
                Log::info("Set mail config {$configKey} from database settings");
            }
        }
    }
}