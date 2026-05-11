<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Setting;

class SetTimezone
{
    public function handle($request, Closure $next)
    {
        $timezone = Setting::get('timezone', 'Asia/Jakarta');
        date_default_timezone_set($timezone);
        config(['app.timezone' => $timezone]);
        
        return $next($request);
    }
}