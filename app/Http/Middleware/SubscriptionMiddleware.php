<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Support\AuthToken;
use App\Models\BidanSubscription;
use Carbon\Carbon;

class SubscriptionMiddleware
{
    /**
     * Handle an incoming request.
     * Check if bidan has active subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            [$userId, $role] = AuthToken::uidRoleOrFail($request);
            
            // Only check for bidan role
            if ($role !== 'bidan') {
                return $next($request);
            }
            
            // Check for active subscription
            $subscription = BidanSubscription::where('user_id', $userId)
                ->where('status', 'active')
                ->whereDate('end_date', '>=', Carbon::today())
                ->first();
            
            if (!$subscription) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Subscription tidak aktif atau sudah berakhir. Silakan perpanjang subscription Anda.',
                    'code' => 'SUBSCRIPTION_REQUIRED',
                ], 403);
            }
            
            // Add subscription info to request for later use
            $request->attributes->set('subscription', $subscription);
            
            return $next($request);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication failed',
            ], 401);
        }
    }
}
