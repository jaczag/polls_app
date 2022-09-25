<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Auth\LoginRequest;
use App\Http\Requests\v1\Auth\RegisterRequest;
use App\Http\Resources\v1\UserResource;
use App\Models\User;
use App\Services\v1\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    /**
     * @param RegisterRequest $request
     * @param UserService $service
     * @return JsonResponse
     */
    public function register(RegisterRequest $request, UserService $service): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        try {
            $service->setUser()->assignData($data);
            return $this->successResponse(null, __('messages.Account created successfully'));
        } catch (Exception $exception) {
            reportError($exception);
        }

        return $this->errorResponse();
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            if (!Auth::attempt($data)) {
                return $this->errorResponse(__('messages.Invalid credentials or User does not exist'));
            }

            return $this->successResponse([
                'token' => Auth::user()->createToken('auth')->plainTextToken,
                'user' => UserResource::make(Auth::user())
            ]);
        } catch (Exception $exception) {
            reportError($exception);
        }

        return $this->errorResponse(__('messages.Something went wrong.'));
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            Auth::user()->tokens()->delete();
            return $this->successResponse();
        } catch (Exception $exception) {
            reportError($exception);
        }
        return $this->errorResponse(__('messages.Something went wrong.')
        );
    }
}
