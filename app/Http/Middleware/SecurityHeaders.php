<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; " .
            "img-src 'self' data: https://*.tile.openstreetmap.org https://unpkg.com; " .
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://unpkg.com; " .
            "font-src 'self' https://fonts.gstatic.com; " .
            "script-src 'self' 'unsafe-inline' https://unpkg.com https://www.gstatic.com https://*.firebaseio.com; " .
            "connect-src 'self' https://unpkg.com https://www.gstatic.com https://*.firebaseio.com wss://*.firebaseio.com https://*.googleapis.com; " .
            "frame-src 'self' https://*.firebaseio.com;"
        );

        return $response;
    }
}