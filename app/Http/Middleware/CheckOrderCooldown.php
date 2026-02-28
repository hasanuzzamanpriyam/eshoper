<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\OrderIpRestriction;
use Carbon\Carbon;

class CheckOrderCooldown
{
    public function handle($request, Closure $next)
    {
        $ipAddress = $request->ip();
        $cooldownMinutes = 5;

        $lastOrder = OrderIpRestriction::where('ip_address', $ipAddress)
            ->orderBy('last_order_time', 'desc')
            ->first();

        if ($lastOrder && Carbon::now()->diffInMinutes($lastOrder->last_order_time) < $cooldownMinutes) {
            $remainingMinutes = $cooldownMinutes - Carbon::now()->diffInMinutes($lastOrder->last_order_time);
            return response()->json([
                'success' => false,
                'message' => "You can place another order after {$remainingMinutes} minutes."
            ], 403);
        }

        return $next($request);
    }
}
