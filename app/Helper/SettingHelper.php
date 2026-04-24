<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingHelper
{
    public static function get($key, $default = null)
    {
        try {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }
    
    public static function getCompanyName()
    {
        return self::get('company_name', 'PT. NAMA PERUSAHAAN');
    }
    
    public static function getSystemName()
    {
        return self::get('system_name', 'Sistem Manajemen Aset IT');
    }
    
    public static function getCompanyLogo()
    {
        try {
            $logo = self::get('company_logo');
            if ($logo && file_exists(storage_path('app/public/' . $logo))) {
                return asset('storage/' . $logo);
            }
        } catch (\Exception $e) {}
        return null;
    }
    
    public static function getFavicon()
    {
        try {
            $favicon = self::get('system_favicon');
            if ($favicon && file_exists(storage_path('app/public/' . $favicon))) {
                return asset('storage/' . $favicon);
            }
        } catch (\Exception $e) {}
        return null;
    }
    
    public static function getDateFormat()
    {
        return self::get('date_format', 'd/m/Y');
    }
    
    public static function getCurrencySymbol()
    {
        return self::get('currency_symbol', 'Rp');
    }
    
    public static function getMaintenanceReminderDays()
    {
        return self::get('maintenance_reminder_days', 7);
    }
}