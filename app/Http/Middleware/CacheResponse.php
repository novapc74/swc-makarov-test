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
            $key = "user_{$userId}_" . md5($request->fullUrl());
            $tag = "user_{$userId}";

            if (Cache::tags([$tag])->has($key)) {
                $response = Cache::tags([$tag])->get($key);
                $response->headers->set('X-Cache', 'HIT');
                return $response;
            }

            $response = $next($request);

            Cache::tags([$tag])->put($key, $response, $ttl);

            $response->headers->set('X-Cache', 'MISS');
            return $response;
        }

        $response = $next($request);
        $response->headers->set('X-Cache', 'MISS');
        return $response;
    }

}
