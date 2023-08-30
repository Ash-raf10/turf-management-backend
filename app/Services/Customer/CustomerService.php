<?php

namespace App\Services\Customer;

use App\Models\User;
use App\Facades\OtpFacade;
use App\Events\UserCreated;
use App\Services\GlobalType;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\InternalResponse;
use App\Traits\InternalResponseObject;
use Illuminate\Support\Facades\Hash;

class CustomerService
{
    use InternalResponse;

    public function __construct(protected UserService $userService)
    {
    }

    public function registerUser(array $requestData): InternalResponseObject
    {
        DB::beginTransaction();
        $requestData['password'] =  Hash::make($requestData['password']);

        $newUser = User::create($requestData);

        Log::info("Created User");
        Log::info(json_encode($newUser));

        $otpResponse = $this->userService->generateOtpForUser($newUser, GlobalType::getOtpType('Registration'));
        if (!$otpResponse->success) {
            DB::rollBack();
            return $this->response(false);
        }
        DB::commit();
        Log::info("Dispatch User Created Event, Send OTP");
        UserCreated::dispatch($newUser, $otpResponse->data);

        $otpResponse = OtpFacade::filterOtpResponse($otpResponse->data);

        return $this->response(true, $otpResponse);
    }
}
