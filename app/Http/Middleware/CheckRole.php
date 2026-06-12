<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Session::has('api_token')) {
            return redirect()->route('login');
        }

        $userRole = Session::get('user_role', 'peserta');

        foreach ($roles as $role) {
            if ($userRole === $role) {
                return $next($request);
            }
        }

        return response()->view('errors.403', [], 403);
    }
}
