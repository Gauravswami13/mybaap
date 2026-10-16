<?php

namespace App\Http\Middleware;

use App\Models\AppSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAppIsOnline
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('api/v1/payments/webhook', 'api/v1/app/bootstrap', 'api/v1/settings/public')) {
            return $next($request);
        }

        try {
            $enabled = AppSetting::flag('maintenance_mode');
        } catch (\Throwable) {
            $enabled = false;
        }

        if (! $enabled) {
            return $next($request);
        }

        $message = AppSetting::getValue('maintenance_message', 'App is under maintenance.');

        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => (object) [],
        ], 503);
    }
}
