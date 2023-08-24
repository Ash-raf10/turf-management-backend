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
        $user = $this->customerService->registerUser($request->validated());

        return $this->sendResponse(true, $user, __("Registration Successfull, Please verify the OTP"), 201, 6001);
    }
}
