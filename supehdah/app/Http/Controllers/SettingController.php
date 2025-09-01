<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display the application settings page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Group settings by their group
        $general = Setting::getGroup('general');
        $email = Setting::getGroup('email');
        $appearance = Setting::getGroup('appearance');
        $security = Setting::getGroup('security');
        $admin = auth()->user(); // Add the admin user
        
        return view('admin.application_settings', compact('general', 'email', 'appearance', 'security', 'admin'));
    }
    
    /**
     * Update general settings
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:1000',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);
        
        $settings = [
            'site_name' => $request->site_name,
            'site_description' => $request->site_description,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'address' => $request->address,
        ];
        
        foreach ($settings as $key => $value) {
            Setting::set($key, $value, 'general');
        }
        
        // Log this action
        SystemLog::log(
            'Updated general settings',
            [
                'settings' => array_keys($settings),
            ],
            'success'
        );
        
        return redirect()->route('admin.settings')->with('success', 'General settings updated successfully');
    }
    
    /**
     * Update email settings
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateEmail(Request $request)
    {
        $request->validate([
            'mail_driver' => 'required|string|in:smtp,sendmail,mailgun',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|in:tls,ssl,',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
        ]);
        
        $settings = [
            'mail_driver' => $request->mail_driver,
            'mail_host' => $request->mail_host,
            'mail_port' => $request->mail_port,
            'mail_username' => $request->mail_username,
            'mail_encryption' => $request->mail_encryption,
            'mail_from_address' => $request->mail_from_address,
            'mail_from_name' => $request->mail_from_name,
        ];
        
        // Only update password if provided
        if ($request->filled('mail_password')) {
            $settings['mail_password'] = $request->mail_password;
        }
        
        foreach ($settings as $key => $value) {
            Setting::set($key, $value, 'email');
        }
        
        // Log this action
        SystemLog::log(
            'Updated email settings',
            [
                'settings' => array_keys($settings),
            ],
            'success'
        );
        
        return redirect()->route('admin.settings')->with('success', 'Email settings updated successfully');
    }
    
    /**
     * Update appearance settings
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAppearance(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:1024',
            'primary_color' => 'nullable|string|max:10',
            'secondary_color' => 'nullable|string|max:10',
        ]);
        
        $settings = [
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
        ];
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('public/settings');
            $settings['logo'] = str_replace('public/', 'storage/', $logoPath);
        }
        
        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $faviconPath = $request->file('favicon')->store('public/settings');
            $settings['favicon'] = str_replace('public/', 'storage/', $faviconPath);
        }
        
        foreach ($settings as $key => $value) {
            Setting::set($key, $value, 'appearance');
        }
        
        // Log this action
        SystemLog::log(
            'Updated appearance settings',
            [
                'settings' => array_keys($settings),
            ],
            'success'
        );
        
        return redirect()->route('admin.settings')->with('success', 'Appearance settings updated successfully');
    }
    
    /**
     * Update security settings
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSecurity(Request $request)
    {
        $request->validate([
            'maintenance_mode' => 'nullable|boolean',
            'enable_registration' => 'nullable|boolean',
            'enable_password_reset' => 'nullable|boolean',
            'max_login_attempts' => 'nullable|integer|min:3|max:10',
            'session_lifetime' => 'nullable|integer|min:10|max:1440',
        ]);
        
        $settings = [
            'maintenance_mode' => $request->has('maintenance_mode') ? 1 : 0,
            'enable_registration' => $request->has('enable_registration') ? 1 : 0,
            'enable_password_reset' => $request->has('enable_password_reset') ? 1 : 0,
            'max_login_attempts' => $request->max_login_attempts ?? 5,
            'session_lifetime' => $request->session_lifetime ?? 120,
        ];
        
        foreach ($settings as $key => $value) {
            Setting::set($key, $value, 'security');
        }
        
        // Log this action
        SystemLog::log(
            'Updated security settings',
            [
                'settings' => array_keys($settings),
            ],
            'success'
        );
        
        return redirect()->route('admin.settings')->with('success', 'Security settings updated successfully');
    }
}
