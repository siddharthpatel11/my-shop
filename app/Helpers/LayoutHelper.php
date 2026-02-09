<?php

namespace App\Helpers;

use App\Models\LayoutSetting;

class LayoutHelper
{
    public static function get($side, $key, $default = null)
    {
        $setting = LayoutSetting::where('side', $side)->where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        $value = $setting->value;

        // Try to decode JSON
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return $value;
    }

    public static function getAll($side)
    {
        $settings = LayoutSetting::where('side', $side)->get();
        $result = [];

        foreach ($settings as $setting) {
            $value = $setting->value;
            $decoded = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $result[$setting->key] = $decoded;
            } else {
                $result[$setting->key] = $value;
            }
        }

        return $result;
    }
}
