<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Setting;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Share settings with all views
        View::composer('*', function ($view) {
            $view->with([
                'companyName' => $this->getSetting('company_name', 'PT. NAMA PERUSAHAAN'),
                'systemName' => $this->getSetting('system_name', 'Sistem Manajemen Aset IT'),
                'companyLogo' => $this->getLogo(),
                'favicon' => $this->getFavicon(),
            ]);
        });
    }
    
    private function getSetting($key, $default = null)
    {
        try {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }
    
    private function getLogo()
    {
        try {
            $logo = $this->getSetting('company_logo');
            if ($logo && file_exists(storage_path('app/public/' . $logo))) {
                return asset('storage/' . $logo);
            }
        } catch (\Exception $e) {}
        return null;
    }
    
    private function getFavicon()
    {
        try {
            $favicon = $this->getSetting('system_favicon');
            if ($favicon && file_exists(storage_path('app/public/' . $favicon))) {
                return asset('storage/' . $favicon);
            }
        } catch (\Exception $e) {}
        return null;
    }

    public function register()
    {
        //
    }
}