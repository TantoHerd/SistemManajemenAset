<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ApiKey;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key') ?? $request->query('api_key');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API Key required. Please provide X-API-Key header or api_key parameter.'
            ], 401);
        }

        $key = ApiKey::where('key', $apiKey)->first();

        if (!$key) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API Key'
            ], 401);
        }

        if (!$key->isValid($request->ip())) {
            return response()->json([
                'success' => false,
                'message' => 'API Key expired or inactive'
            ], 401);
        }

        // Update last used
        $key->update(['last_used_at' => now()]);

        // Attach user to request
        $request->merge(['api_user' => $key->user]);

        return $next($request);
    }
}