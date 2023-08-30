<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Auth\CustomerUserRegisterRequest;
use App\Services\Customer\CustomerService;

class CustomerController extends BaseController
{

    public function __construct(private CustomerService $customerService)
    {
    }

    public function register(CustomerUserRegisterRequest $request)
    {
        $otpResponse = $this->customerService->registerUser($request->validated());

        if (!$otpResponse->success) {
            return $this->sendResponse(false, "", __("Registration Failed, Please try again"), 404, 4001);
        }

        return $this->sendResponse(
            true,
            $otpResponse->data,
            __("Registration Successfull, Please verify the OTP"),
            201,
            6001
        );
    }
}
