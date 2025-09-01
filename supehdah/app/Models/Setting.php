<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Get a setting value by key
     *
     * @param string $key The setting key
     * @param mixed $default The default value if setting not found
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        return $setting->value;
    }

    /**
     * Set a setting value
     *
     * @param string $key The setting key
     * @param mixed $value The value to set
     * @param string $group Optional group name
     * @return Setting
     */
    public static function set($key, $value, $group = 'general')
    {
        $setting = self::firstOrNew(['key' => $key]);
        $setting->value = $value;
        $setting->group = $group;
        $setting->save();
        
        return $setting;
    }

    /**
     * Get all settings in a group
     *
     * @param string $group The settings group
     * @return array
     */
    public static function getGroup($group)
    {
        $settings = self::query()->where('group', $group)->get();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->value;
        }
        
        return $result;
    }
}
