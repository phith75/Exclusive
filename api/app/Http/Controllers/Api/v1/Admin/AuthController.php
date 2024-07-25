<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     */
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login', 'register', 'refresh']]);
    }

    /**
     * Get a JWT via given credentials.
     */
    public function login(AuthRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        if (! $token = auth()->attempt($credentials)) {
            return $this->responseError(Response::HTTP_UNAUTHORIZED, 'Unauthenticated');
        }
        if (auth()->user()->role == UserRole::USER->value) {
            return $this->responseError(Response::HTTP_FORBIDDEN, 'Forbidden: Unauthorized');
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     */
    public function me(): JsonResponse
    {
        return $this->responseSuccess(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return $this->responseSuccess(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     */
    public function refresh(): JsonResponse
    {   
        return $this->responseRefreshToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     */
    protected function respondWithToken($token): JsonResponse
    {
        return $this->responseSuccess([
            'access_token' => $token,
            'user' => AuthResource::make(auth()->user()),
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * config('jwt.ttl', 10),
        ]);
    }

    protected function responseRefreshToken(string $token): JsonResponse
    {
        return $this->responseSuccess([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * config('jwt.ttl', 10),
        ]);
    }
}
