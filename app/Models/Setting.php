<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type', 'description', 'locale'];

    /**
     * 根據鍵取得設定值
     *
     * @param string $key
     * @param mixed|null $default
     * @param string|null $locale
     * @return mixed
     */
    public static function getValue(string $key, $default = null, ?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        
        $setting = self::where('key', $key)
            ->where('locale', $locale)
            ->first();
        
        if (!$setting) {
            // Fallback to default locale if not found
            if ($locale !== config('app.fallback_locale')) {
                $fallbackSetting = self::where('key', $key)
                    ->where('locale', config('app.fallback_locale'))
                    ->first();
                    
                if ($fallbackSetting) {
                    return self::castValue($fallbackSetting);
                }
            }
            
            return $default;
        }
        
        return self::castValue($setting);
    }
    
    /**
     * 設定值
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @param string|null $description
     * @param string|null $locale
     * @return void
     */
    public static function setValue(string $key, $value, string $type = 'string', ?string $description = null, ?string $locale = null): void
    {
        $locale = $locale ?? app()->getLocale();
        
        self::updateOrCreate(
            ['key' => $key, 'locale' => $locale],
            [
                'value' => $value,
                'type' => $type,
                'description' => $description
            ]
        );
    }
    
    /**
     * 根據類型轉換值
     *
     * @param Setting $setting
     * @return mixed
     */
    protected static function castValue(Setting $setting)
    {
        return match($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'float' => (float) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }
}
