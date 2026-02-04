<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Http\Resources\Admin\Auth\LoginResource;
use App\Http\Responses\SuccessResponse;
use App\Service\Admin\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function login(LoginRequest $request): LoginResource
    {
        $validated = $request->validated();
        $email = $validated['email'];
        $password = $validated['password'];
        return new LoginResource($this->authService->login($email, $password));
    }

    public function logout(Request $request): SuccessResponse
    {
        $this->authService->logout($request->user());
        return (new SuccessResponse("Logout successfully"));
    }
}
