<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // OWASP Recommended Security Headers
        $response->headers->set('X-Frame-Options', 'DENY', true);
        $response->headers->set('X-Content-Type-Options', 'nosniff', true);
        $response->headers->set('X-XSS-Protection', '1; mode=block', true);
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin', true);
        
        // Prevent browser caching (back button exposure protection)
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true);
        $response->headers->set('Pragma', 'no-cache', true);
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT', true);
        
        // Strictly enforce HTTPS if session is secure
        if (config('session.secure')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload', true);
        }

        // Strict Content Security Policy (CSP)
        // Allow self, Google Fonts, Tailwind CDN, jQuery, DataTables, and connections to the FastAPI backend
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://code.jquery.com https://cdn.datatables.net; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.datatables.net; " .
               "font-src 'self' https://fonts.gstatic.com; " .
               "img-src 'self' data: https:; " .
               "connect-src 'self' http://localhost:8085; " .
               "frame-ancestors 'none'; " .
               "form-action 'self';";
        
        $response->headers->set('Content-Security-Policy', $csp, true);

        return $response;
    }
}
