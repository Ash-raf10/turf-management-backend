<?php

namespace App\Http\Controllers\Api\V1;

use App\Facades\OtpFacade;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\UserResource;
use App\Services\GlobalType;
use App\Services\UserService;

class AuthController extends BaseController
{
    public function __construct(private AuthService $authService)
    {
    }

    /**
     * login function
     *
     * @param  LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request, UserService $userService): JsonResponse
    {
        [$token, $user] = $this->authService->login($request->validated());

        if (!$token || !$user) {
            return $this->sendResponse(
                false,
                "",
                ("$request->identifier_type and password did not match"),
                403,
                6000
            );
        }

        if (!$user->otp_verified) {
            $otpResponse = $userService->generateOtpForUser($user, GlobalType::getOtpType('Login'));

            if (!$otpResponse->success) {
                return $this->sendResponse(true, "", __("Something went wrong, Please try agian"), 404, 4001);
            }
            $userService->sendOtp($user, $otpResponse->data);
            $otpResponse = OtpFacade::filterOtpResponse($otpResponse->data);
            return $this->sendResponse(true, $otpResponse, __("Before Login, please verify the OTP"), 307, 6001);
        }

        return $this->sendResponse(true, [
            'user' => new UserResource($user),
            'Authorization' => "Bearer $token"
        ], __("Successfully Logged In"));
    }




    /**
     * logout user
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::logout();

        return $this->sendResponse(true, "", __("Successfully logged out"), 200, 6000);
    }

    public function me()
    {
        $user = $this->authService->getAuthUser();

        return $this->sendResponse(true, $user, "", 200, 0000);
    }

    /**
     * refresh token
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        $token = $this->authService->refreshToken();
        $user = $this->authService->getAuthUser();

        if (!$token || !$user) {
            return $this->sendResponse(false, "", __("Unauthorized"), 403, 6000);
        }

        return $this->sendResponse(true, [
            'user' => $user,
            'Authorization' => "Bearer $token"
        ]);
    }
}
