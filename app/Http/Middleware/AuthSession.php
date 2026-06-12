<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class AuthSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = Session::get('api_token');
        
        if (!$token) {
            return redirect()->route('login');
        }

        // Validate token with FastAPI backend
        try {
            $backendUrl = env('FASTAPI_URL', 'http://localhost:8085/api/v1');
            $response = Http::withToken($token)->timeout(3)->get($backendUrl . '/users/me');
            
            if ($response->failed()) {
                // Token has expired or is invalid on the backend
                Session::invalidate();
                Session::regenerateToken();
                return redirect()->route('login')
                    ->withErrors(['message' => 'Session expired. Please log in again.'])
                    ->withoutCookie(config('session.cookie'));
            }
        } catch (\Exception $e) {
            // If the backend is temporarily unreachable, we could choose to allow local session, 
            // or redirect. To be secure, we will allow it but log or keep the local session.
            // Let's fallback to allowing local session if we cannot connect, to avoid complete downtime if FastAPI is reloading.
        }

        return $next($request);
    }
}
