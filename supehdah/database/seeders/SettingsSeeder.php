<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // General Settings
        $general = [
            'site_name' => 'VetBook',
            'site_description' => 'Veterinary Clinic Management System',
            'contact_email' => 'contact@vetbook.com',
            'contact_phone' => '+639123456789',
            'address' => '123 Main St, Manila, Philippines',
        ];
        
        foreach ($general as $key => $value) {
            Setting::set($key, $value, 'general');
        }
        
        // Email Settings
        $email = [
            'mail_driver' => 'smtp',
            'mail_host' => 'smtp.mailtrap.io',
            'mail_port' => '2525',
            'mail_username' => '',
            'mail_password' => '',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'noreply@vetbook.com',
            'mail_from_name' => 'VetBook',
        ];
        
        foreach ($email as $key => $value) {
            Setting::set($key, $value, 'email');
        }
        
        // Appearance Settings
        $appearance = [
            'primary_color' => '#4F46E5',
            'secondary_color' => '#2563EB',
        ];
        
        foreach ($appearance as $key => $value) {
            Setting::set($key, $value, 'appearance');
        }
        
        // Security Settings
        $security = [
            'maintenance_mode' => '0',
            'enable_registration' => '1',
            'enable_password_reset' => '1',
            'max_login_attempts' => '5',
            'session_lifetime' => '120',
        ];
        
        foreach ($security as $key => $value) {
            Setting::set($key, $value, 'security');
        }
    }
}
