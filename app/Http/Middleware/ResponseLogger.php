<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ResponseLogger
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        if (Config::get("app.enable_response_log")) {
            $url = $request->getRequestUri();
            if (str_starts_with($url, "/api")) {
                $data = json_decode($response->getContent(), true);
                $status = $response->getStatusCode();
                Log::info("response($status) of $url", [
                    "response" => $data,
                ]);
            }
        }
        return $response;
    }
}
