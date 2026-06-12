<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class FastAPIAuthController extends Controller
{
    protected $backendUrl = 'http://localhost:8085/api/v1';

    public function showLogin()
    {
        if (Session::has('api_token')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|max:100',
        ]);

        $response = Http::asForm()->post($this->backendUrl . '/login/access-token', [
            'username' => $request->email,
            'password' => $request->password,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            Session::invalidate();
            Session::regenerateToken();
            Session::put('api_token', $data['access_token']);

            $userResponse = Http::withToken($data['access_token'])->timeout(5)->get($this->backendUrl . '/users/me');
            if ($userResponse->successful()) {
                $userData = $userResponse->json();
                Session::put('user_id', $userData['id'] ?? null);
                Session::put('user_role', $userData['role'] ?? 'peserta');
                Session::put('user_name', $userData['full_name'] ?? $userData['email']);
                Session::put('user_email', $userData['email'] ?? '');
            } else {
                Session::put('user_id', null);
                Session::put('user_role', 'peserta');
                Session::put('user_name', 'User');
                Session::put('user_email', '');
            }

            return redirect()->route('dashboard')->with('success', 'Welcome back!');
        }

        return back()->withErrors(['message' => $response->json()['detail'] ?? 'Login failed']);
    }

    public function showRegister()
    {
        if (Session::has('api_token')) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:100',
                'regex:/[a-zA-Z]/',
                'regex:/[0-9]/',
            ],
            'full_name' => 'required|string|max:255',
        ], [
            'password.regex' => 'Password must contain at least one letter and one number (min 8 characters).',
        ]);

        $response = Http::post($this->backendUrl . '/users/register', [
            'email' => $request->email,
            'password' => $request->password,
            'full_name' => $request->full_name,
            'role' => 'peserta',
        ]);

        if ($response->successful()) {
            return redirect()->route('login')->with('success', 'Registration successful! Please check your email to verify.');
        }

        return back()->withErrors(['message' => $response->json()['detail'] ?? 'Registration failed']);
    }

    public function dashboard()
    {
        $role = Session::get('user_role', 'peserta');

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'dosen') {
            return redirect()->route('dosen.dashboard');
        } else {
            return redirect()->route('peserta.dashboard');
        }
    }

    public function logout(Request $request)
    {
        Session::invalidate();
        Session::regenerateToken();
        
        $cookieName = config('session.cookie');
        return redirect()->route('login')
            ->with('success', 'Logged out successfully')
            ->withoutCookie($cookieName);
    }

    public function verify($token)
    {
        $response = Http::post($this->backendUrl . '/users/verify-email/' . $token);

        if ($response->successful()) {
            return view('auth.verify', ['success' => true, 'status' => 'completed']);
        }

        return view('auth.verify', [
            'success' => false,
            'status' => 'completed',
            'message' => $response->json()['detail'] ?? 'Verification failed'
        ]);
    }

    public function showCleanVerify()
    {
        $token = Session::get('verification_token');
        if (!$token) {
            return redirect()->route('login')->withErrors(['message' => 'Verification token missing or expired']);
        }
        return view('auth.verify', ['token' => $token, 'status' => 'pending']);
    }

    public function verifyConfirm(Request $request)
    {
        $token = $request->token;
        if (!$token) {
            return view('auth.verify', ['success' => false, 'status' => 'completed', 'message' => 'Token missing']);
        }

        $response = Http::post($this->backendUrl . '/users/verify-email/' . $token);

        if ($response->successful()) {
            return view('auth.verify', ['success' => true, 'status' => 'completed']);
        }

        return view('auth.verify', [
            'success' => false,
            'status' => 'completed',
            'message' => $response->json()['detail'] ?? 'Verification failed'
        ]);
    }

    public function showForgotPassword()
    {
        if (Session::has('api_token')) {
            return redirect()->route('dashboard');
        }
        return view('auth.forgot_password');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $response = Http::post($this->backendUrl . '/password-recovery/' . $request->email);

        if ($response->successful()) {
            return back()->with('success', 'Password reset link sent to your email');
        }

        return back()->withErrors(['message' => $response->json()['detail'] ?? 'Failed to send reset link']);
    }

    public function resetPasswordLink($token)
    {
        Session::flash('reset_token', $token);
        return redirect()->route('password.reset.clean');
    }

    public function showResetPassword()
    {
        $token = Session::get('reset_token');
        if (!$token) {
            return redirect()->route('login')->withErrors(['message' => 'Reset token missing or expired']);
        }
        return view('auth.reset_password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:100',
                'regex:/[a-zA-Z]/',
                'regex:/[0-9]/',
                'confirmed'
            ],
        ], [
            'password.regex' => 'Password must contain at least one letter and one number (min 8 characters).',
        ]);

        $token = $request->token;
        if (!$token) {
            return back()->withErrors(['message' => 'Token missing']);
        }

        $response = Http::post($this->backendUrl . '/reset-password/', [
            'token' => $token,
            'new_password' => $request->password,
        ]);

        if ($response->successful()) {
            return redirect()->route('login')->with('success', 'Password reset successfully');
        }

        return back()->withErrors(['message' => $response->json()['detail'] ?? 'Reset failed']);
    }
}
