<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Company\CompanyAndUserRegisterRequest;
use App\Services\Company\CompanyService;

class CompanyController extends BaseController
{

    public function __construct(private CompanyService $companyService)
    {
    }

    public function register(CompanyAndUserRegisterRequest $request)
    {
        $response = $this->companyService->registerUser($request->validated());

        if (!$response->success) {
            return $this->sendResponse(false, "", __("Registration Failed, Please try again"), 404, 4001);
        }

        return $this->sendResponse(
            true,
            $response->data,
            __("Registration Successfull, Please verify the OTP"),
            201,
            6001
        );
    }
}
