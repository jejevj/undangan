<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralConfig extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get($key, $default = null)
    {
        $config = self::where('key', $key)->first();
        return $config ? $config->value : $default;
    }

    public static function set($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function getAll()
    {
        return self::pluck('value', 'key')->toArray();
    }
}
