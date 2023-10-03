<?php

namespace App\Services\Company;

use App\Models\User;
use App\Facades\OtpFacade;
use App\Events\UserCreated;
use App\Models\Company;
use App\Services\GlobalType;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\InternalResponse;
use App\Traits\InternalResponseObject;
use Illuminate\Support\Facades\Hash;

class CompanyService
{
    use InternalResponse;

    public function __construct(protected UserService $userService)
    {
    }

    public function registerUser(array $requestData): InternalResponseObject
    {
        DB::beginTransaction();
        //company entry
        $companyRequest = $this->mapCompany($requestData);
        $company = Company::create($companyRequest);
        Log::info("Company created");
        Log::info(json_encode($company));
        try {
            $requestData['password'] =  Hash::make($requestData['password']);
            $requestData['company_id'] = $company['id'];
    
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
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error($ex);
            return $this->response(false);
        }  
    }

    private function mapCompany(array $requestData) :array {

        $companyRequest['name'] = $requestData['company_name'];
        $companyRequest['address'] = $requestData['address'];
        $companyRequest['email'] = $requestData['company_email'];
        $companyRequest['phone'] = $requestData['company_phone'];
        $companyRequest['page_url'] = $requestData['page_url'];
        return $companyRequest;
    }
}
