<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Session::has('jwt_token')) {
            return redirect()->route('dashboard');
        }
        return view('login');
    }

    /**
     * Handle authentication request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $authUrl = env('AUTH_SERVICE_URL', 'https://fitlife-auth-service.onrender.com');

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->withoutVerifying()->timeout(30)->post("{$authUrl}/api/v1/auth/login", [
                'email'    => $request->email,
                'password' => $request->password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['authorization']['token'] ?? null;
                $user = $data['user'] ?? null;

                if (!$token) {
                    \Illuminate\Support\Facades\Log::error('Auth Service returned success but no token: ' . $response->body());
                    return redirect()->route('login')->withErrors([
                        'email' => 'Authentication error. Please try again.'
                    ])->withInput($request->only('email'));
                }

                // Save in session
                Session::put('jwt_token', $token);
                Session::put('user', $user);

                return redirect()->route('dashboard')->with('success', 'Logged in successfully!');
            }

            \Illuminate\Support\Facades\Log::error("Auth Service Login Failed: HTTP {$response->status()} - " . $response->body());

            return redirect()->route('login')->withErrors([
                'email' => 'Invalid credentials. Please verify your email and password.'
            ])->withInput($request->only('email'));

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Auth service connection error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors([
                'email' => 'Unable to connect to authentication service. Please try again later.'
            ])->withInput($request->only('email'));
        }
    }

    /**
     * Handle user logout.
     */
    public function logout()
    {
        Session::forget('jwt_token');
        Session::forget('user');
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
}
