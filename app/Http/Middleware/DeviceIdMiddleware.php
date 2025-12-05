<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceIdMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $deviceId = $request->header('X-Device-ID');

        if (!$deviceId) {
            return response()->json([
                'message' => 'X-Device-ID header is required.'
            ], 400);
        }

        $request->merge(['device_id' => $deviceId]);

        return $next($request);
    }
}
