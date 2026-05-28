<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verifies JWT tokens locally using RSA public key (RS256).
 *
 * This middleware replaces the old VerifyJwtFromAuthService middleware
 * which made a synchronous HTTP call to the Auth Service on every request.
 *
 * Now, tokens are verified cryptographically using the RSA public key
 * mounted as a Docker volume — no network call required.
 */
class VerifyJwtLocally
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
                'status'  => 'error',
                'message' => 'Authorization token not provided or invalid format',
            ], 401);
        }

        $token = substr($authHeader, 7);

        try {
            // Read the RSA public key from the Docker-mounted volume
            $publicKeyPath = env('JWT_PUBLIC_KEY_PATH', '/var/www/keys/public.pem');
            $publicKey = file_get_contents($publicKeyPath);

            if (!$publicKey) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'JWT public key not found. Ensure the key volume is mounted.',
                ], 500);
            }

            // Decode and verify the JWT token locally using RS256
            $decoded = \Firebase\JWT\JWT::decode(
                $token,
                new \Firebase\JWT\Key($publicKey, 'RS256')
            );

            // Extract user claims embedded in the token by the Auth Service
            $request->merge([
                'auth_user' => [
                    'id'    => $decoded->sub ?? null,
                    'name'  => $decoded->name ?? null,
                    'email' => $decoded->email ?? null,
                    'role'  => $decoded->role ?? null,
                ],
            ]);

            return $next($request);

        } catch (\Firebase\JWT\ExpiredException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized: Token has expired',
            ], 401);
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized: Invalid token signature',
            ], 401);
        } catch (\Firebase\JWT\BeforeValidException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized: Token is not yet valid',
            ], 401);
        } catch (\UnexpectedValueException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized: Malformed token',
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Token verification failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
