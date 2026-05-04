<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class FastAPIAuthController extends Controller
{
    protected $backendUrl = 'http://localhost:8081/api/v1';

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $response = Http::asForm()->post($this->backendUrl . '/login/access-token', [
            'username' => $request->email,
            'password' => $request->password,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            Session::put('api_token', $data['access_token']);
            return redirect()->route('dashboard')->with('success', 'Welcome back!');
        }

        return back()->withErrors(['message' => $response->json()['detail'] ?? 'Login failed']);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $response = Http::post($this->backendUrl . '/users/register', [
            'email' => $request->email,
            'password' => $request->password,
            'full_name' => $request->full_name,
        ]);

        if ($response->successful()) {
            return redirect()->route('login')->with('success', 'Registration successful! Please check your email to verify.');
        }

        return back()->withErrors(['message' => $response->json()['detail'] ?? 'Registration failed']);
    }

    public function dashboard()
    {
        $token = Session::get('api_token');
        if (!$token) {
            return redirect()->route('login');
        }

        $response = Http::withToken($token)->get($this->backendUrl . '/users/me');

        if ($response->successful()) {
            return view('auth.dashboard', ['user' => $response->json()]);
        }

        Session::forget('api_token');
        return redirect()->route('login')->withErrors(['message' => 'Session expired']);
    }

    public function logout()
    {
        Session::forget('api_token');
        return redirect()->route('login')->with('success', 'Logged out successfully');
    }

    public function verify($token)
    {
        $response = Http::get($this->backendUrl . '/users/verify-email/' . $token);

        if ($response->successful()) {
            return view('auth.verify', ['success' => true]);
        }

        return view('auth.verify', ['success' => false, 'message' => $response->json()['detail'] ?? 'Verification failed']);
    }

    public function showForgotPassword()
    {
        return view('auth.forgot_password');
    }

    public function forgotPassword(Request $request)
    {
        $response = Http::post($this->backendUrl . '/password-recovery/' . $request->email);
        
        if ($response->successful()) {
            return back()->with('success', 'Password reset link sent to your email');
        }

        return back()->withErrors(['message' => $response->json()['detail'] ?? 'Failed to send reset link']);
    }

    public function showResetPassword(Request $request)
    {
        return view('auth.reset_password', ['token' => $request->token]);
    }

    public function resetPassword(Request $request)
    {
        $response = Http::post($this->backendUrl . '/reset-password/', [
            'token' => $request->token,
            'new_password' => $request->password,
        ]);

        if ($response->successful()) {
            return redirect()->route('login')->with('success', 'Password reset successfully');
        }

        return back()->withErrors(['message' => $response->json()['detail'] ?? 'Reset failed']);
    }
}
