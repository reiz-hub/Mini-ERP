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

        $authUrl = env('AUTH_SERVICE_URL', 'http://localhost:8001');

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->post("{$authUrl}/api/v1/auth/login", [
                'email'    => $request->email,
                'password' => $request->password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['authorization']['token'];
                $user = $data['user'];

                // Save in session
                Session::put('jwt_token', $token);
                Session::put('user', $user);

                return redirect()->route('dashboard')->with('success', 'Logged in successfully!');
            }

            $errorMsg = "HTTP {$response->status()} - Body: " . $response->body();
            return back()->withErrors(['email' => $errorMsg])->withInput($request->only('email'));

        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Auth service unreachable: ' . $e->getMessage()])->withInput($request->only('email'));
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
