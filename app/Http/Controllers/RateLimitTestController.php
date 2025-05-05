<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitTestController extends Controller
{
    public function sendMessage(Request $request)
    {
        // or use $request->user()->id if authenticated
        $key = 'send-message:' . $request->ip();

        $executed = RateLimiter::attempt(
            $key,
            $perMinute = 5,
            function () {
                return true;
            }
        );

        if (! $executed) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'success' => false,
                'message' => "Too many attempts! Try again in $seconds seconds."
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully!'
        ]);
    }

    public function clearRateLimit(Request $request)
    {
        $key = 'send-message:' . $request->ip();
        RateLimiter::clear($key);

        return response()->json([
            'success' => true,
            'message' => 'Rate limit cleared.'
        ]);
    }
}
