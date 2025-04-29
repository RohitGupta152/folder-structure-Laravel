<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;

// class AddRequestContext
// {
//     public function handle(Request $request, Closure $next)
//     {
//         Context::add('url', $request->fullUrl());
//         Context::add('trace_id', (string) Str::uuid());

//         return $next($request);
//     }
// }


class AddRequestContext
{
    public function handle(Request $request, Closure $next)
    {
        // Store contextual information in the container or a global singleton
        App::instance('request-context', [
            'user_id' => optional($request->user())->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_id' => uniqid('req_', true),
        ]);

        return $next($request);
    }
}
