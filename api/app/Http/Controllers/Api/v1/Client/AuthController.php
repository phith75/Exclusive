<?php

namespace App\Http\Controllers\Api\v1\Client;

use App\Enum\Gender;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Resources\AuthResource;
use App\Http\Resources\UserResource;
use App\Models\Staff;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    private $userService;

    /**
     * Create a new AuthController instance.
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware('auth:api', ['except' => ['login', 'register', 'forgotPassword', 'resetPassword', 'redirectToGoogle', 'handleGoogleCallback']]);
    }

    /**
     * Get a JWT via given credentials.
     */
    public function login(AuthRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        if (!$token = auth()->attempt($credentials)) {
            return $this->responseError(Response::HTTP_UNAUTHORIZED, 'Unauthenticated');
        }

        return $this->respondWithToken($token);
    }

    /**
     * Register a User.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $dataStaff = Staff::where('email', $validatedData['email'])->first();
        $validatedData['user_code'] = $dataStaff->user_code;
        $validatedData['name'] = $dataStaff->name;
        $validatedData['password'] = Hash::make($request->password);
        $user = $this->userService->create($validatedData);
        auth()->login($user);

        $token = auth()->attempt($request->only('email', 'password'));

        return $this->respondWithToken($token);
    }

    /**
     * Redirect to Google for authentication.
     */
    public function redirectToGoogle()
    {
        $url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();

        return $this->responseSuccess(['url' => $url]);
    }

    /**
     * Handle the callback from Google.
     */
    public function handleGoogleCallback(Request $request)
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $dataStaff = Staff::where('email', $googleUser->getEmail())->first();
        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $dataStaff->name,
                'user_code' => $dataStaff->user_code,
                'google_id' => $googleUser->getId()
            ]
        );

        $token = JWTAuth::fromUser($user);

        return redirect('http://localhost:3000/google/callback?token=' . $token);
    }

    /**
     * Get the authenticated User.
     */
    public function me(): JsonResponse
    {
        return $this->responseSuccess(UserResource::make(auth()->user()));
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $id = auth()->user()->id;
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|min:10|max:16',
            'gender' => 'nullable|integer|in:' . implode(',', Gender::getValues()),
            'address' => 'nullable|string|max:255',
            'province_id' => 'nullable|exists:provinces,id',
            'district_id' => 'nullable|exists:districts,id',
            'ward_id' => 'nullable|exists:wards,id',
            'date_of_birth' => 'nullable|date|before:today|after:1900-01-01|date_format:Y-m-d',
        ]);
        $updatedUser = $this->userService->update($id, $validatedData);

        return $this->responseSuccess($updatedUser);
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
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Forgot password request.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->responseError(Response::HTTP_BAD_REQUEST, 'Email not found');
        }

        $token = Password::createToken($user);
        $user->notify(new ResetPasswordNotification($token));

        return $this->responseSuccess(['message' => 'Password reset link sent']);
    }

    /**
     * Handle the reset password request.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return $this->responseSuccess(['message' => __($status)]);
        }

        return $this->responseError(Response::HTTP_BAD_REQUEST, 'email: ' . __($status));
    }

    /**
     * Handle the change password request.
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->responseError(Response::HTTP_BAD_REQUEST, 'Current password is incorrect');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return $this->responseSuccess(['message' => 'Password successfully changed']);
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
            'expires_in' => auth()->factory()->getTTL() * config('jwt.ttl', 60),
        ]);
    }
}
