<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * POST /api/v1/auth/register
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'sometimes|string|in:admin,manager,trainer,front_desk,member',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role ?? 'member',
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status'  => 'success',
            'message' => 'User registered successfully',
            'user'    => $user,
            'authorization' => [
                'token' => $token,
                'type'  => 'bearer',
            ],
        ], 201);
    }

    /**
     * Authenticate user and return a JWT token.
     *
     * POST /api/v1/auth/login
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = JWTAuth::user();

        return response()->json([
            'status'  => 'success',
            'message' => 'Login successful',
            'user'    => $user,
            'authorization' => [
                'token' => $token,
                'type'  => 'bearer',
            ],
        ]);
    }

    /**
     * Log the user out (invalidate the token).
     *
     * POST /api/v1/auth/logout
     */
    public function logout(): JsonResponse
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'status'  => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Get the authenticated user's profile.
     *
     * GET /api/v1/auth/me
     */
    public function me(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'user'   => JWTAuth::user(),
        ]);
    }
}
