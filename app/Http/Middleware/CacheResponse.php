<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, int $ttl = 600): Response
    {
        if ($request->isMethod('get') && auth()->check()) {
            $userId = auth()->id();
            $url = $request->fullUrl();
            $key = "user_{$userId}_" . md5($url);

            return Cache::tags(["user_{$userId}_tasks"])->remember($key, $ttl, function () use ($next, $request) {
                return $next($request);
            });
        }

        return $next($request);
    }
}
