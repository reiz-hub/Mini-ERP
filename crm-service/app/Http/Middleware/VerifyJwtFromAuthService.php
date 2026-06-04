<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class VerifyJwtFromAuthService
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authorization token not provided or invalid format',
            ], 401);
        }

        try {
            // Call the auth-service to verify the token and get user profile
            $response = Http::withHeaders([
                'Authorization' => $authHeader,
                'Accept' => 'application/json',
            ])->timeout(30)->get(env('AUTH_SERVICE_URL', 'https://localhost:8001') . '/api/v1/auth/me');

            if ($response->successful()) {
                $userData = $response->json('user');
                // Inject the user details into the request for access in controllers
                $request->merge(['auth_user' => $userData]);
                return $next($request);
            }

            // If auth-service returned unauthenticated (e.g. 401)
            if ($response->status() === 401) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized: Invalid or expired token',
                ], 401);
            }

            // Any other response code from auth-service
            return response()->json([
                'status' => 'error',
                'message' => 'Auth service returned an error status: ' . $response->status(),
            ], 500);

        } catch (\Exception $e) {
            // Connection errors, timeouts, etc.
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication service is unreachable: ' . $e->getMessage(),
            ], 500);
        }
    }
}
