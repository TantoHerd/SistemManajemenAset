<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Log akses (opsional, bisa diaktifkan jika perlu)
            // AuditLog::create([...])
        }

        return $next($request);
    }

    public function terminate($request, $response)
    {
        if (Auth::check()) {
            // Log logout jika perlu
        }
    }
}