<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Facades\OtpFacade;
use App\Services\GlobalType;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\SocialLoginRequest;
use App\Http\Requests\Auth\UpdateMeRequest;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\UserIdentifierRequest;
use Illuminate\Support\Facades\Hash;

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
                4001
            );
        }

        if (!$user->otp_verified) {
            $otpResponse = $userService->generateOtpForUser($user, GlobalType::getOtpType('Login'));

            if (!$otpResponse->success) {
                return $this->sendResponse(true, "", __("Something went wrong, Please try agian"), 404, 4001);
            }
            $userService->sendOtp($user, $otpResponse->data);
            $otpResponse = OtpFacade::filterOtpResponse($otpResponse->data);
            return $this->sendResponse(true, $otpResponse, __("Before Login, please verify the OTP"), 200, 6001);
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

    public function updateMe(UpdateMeRequest $request)
    {
        $user = Auth::user();
        $userModel = User::find($user->id);
       
        $userModel->update($request->validated());

        return $this->sendResponse(true, $userModel, "", 200, 0000);
    }


    public function changeUserIdentifier(UserIdentifierRequest $request)
    {
        $user = Auth::user();
        $userModel = User::find($user->id);

        $validatedData = $request->validated();
        $validatedData['otp_verified'] = 0;
       
        $userModel->update($validatedData);
        
        Auth::logout();

        return $this->sendResponse(true, "", __("Successfully logged out"), 200, 6000);
    }


    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();
        $userModel = User::find($user->id);

        $userModel->update(['password' => Hash::make($request->new_password)]);
               
        Auth::logout();

        return $this->sendResponse(true, "", __("Successfully logged out"), 200, 6000);
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

    public function sociallogin(SocialLoginRequest $request): JsonResponse
    {
        [$token,$user] = $this->authService->ssoUserRegister($request->validated());

        if (!$token) {
            return $this->sendResponse(false, "", __("Login Failed, Please try again"), 404, 4001);
        }

        return $this->sendResponse(true, [
            'user' => new UserResource($user),
            'Authorization' => "Bearer $token"
        ], __("Successfully Logged In"));
    }
}
