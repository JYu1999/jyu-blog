<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type', 'description'];

    /**
     * 根據鍵取得設定值
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
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
     * @return void
     */
    public static function setValue(string $key, $value, string $type = 'string', ?string $description = null): void
    {
        self::updateOrCreate(
            ['key' => $key],
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
