<?php

namespace App\Http\Middleware;

use App\Models\SystemStatus;
use Closure;
use Illuminate\Http\Request;

class CheckSystemStatus
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->routeIs('admin.login*') || $request->routeIs('system.*')) {
            return $next($request);
        }

        if (SystemStatus::isDown('ims')) {
            $user = auth()->user();

            if ($user && in_array($user->role, ['admin', 'superadmin'])) {
                return $next($request);
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'System is currently under maintenance.'], 503);
            }

            $status = SystemStatus::current('ims');
            return response()->view('maintenance', compact('status'), 503);
        }

        return $next($request);
    }
}