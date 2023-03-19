<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class RequestLogger
{
    public function handle(Request $request, Closure $next)
    {
        if (Config::get("app.enable_request_log")) {
            $uri = $request->getRequestUri();
            $method = $request->method();
            Log::info("$uri ($method)", [
                "request" => $request->all(),
            ]);
        }
        return $next($request);
    }
}
