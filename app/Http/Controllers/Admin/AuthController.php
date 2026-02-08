<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Http\Resources\Admin\Auth\LoginResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\AuthService;
use Illuminate\Http\Request;

/**
 * @group Authentication
 * 
 * API endpoints for admin authentication (login/logout).
 */
class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Admin login
     * 
     * Authenticate admin user with email and password. Returns access token and refresh token.
     * 
     * @bodyParam email string required Admin email address. Example: "admin@example.com"
     * @bodyParam password string required Password (min 6 characters). Example: "password123"
     * 
     * @response 200 {"access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...", "refresh_token": "abc123...", "token_type": "Bearer", "expires_in": 3600}
     * @response 401 {"message": "Email hoặc mật khẩu không đúng."}
     * @response 401 {"message": "Tài khoản đã bị khóa."}
     * @response 422 {"message": "Validation error", "errors": {"email": ["The email field is required."], "password": ["The password field is required."]}}
     */
    public function login(LoginRequest $request): LoginResource
    {
        $validated = $request->validated();
        $email = $validated['email'];
        $password = $validated['password'];

        return new LoginResource($this->authService->login($email, $password));
    }

    /**
     * Admin logout
     * 
     * Logout authenticated admin user. Invalidates all refresh tokens.
     * Requires Bearer token authentication.
     * 
     * @response 200 {"message": "Logout successfully"}
     * @response 401 {"message": "Unauthenticated."}
     */
    public function logout(Request $request): SuccessResponse
    {
        $payload = $request->attributes->get('payload') ?? [];

        $this->authService->logout($request->user(), $payload);

        return (new SuccessResponse("Logout successfully"));
    }
}
