<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\Otp;
use App\Facades\OtpFacade;
use App\Services\GlobalType;
use App\Services\UserService;
use App\Http\Requests\OtpRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\V1\BaseController;

class OtpController extends BaseController
{
    /**
     * matchOtp
     *
     * @param  OtpRequest $otpRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function matchOtp(OtpRequest $otpRequest, UserService $userService)
    {
        DB::beginTransaction();
        try {
            $otpRequestData = $otpRequest->validated();
            $otp = Otp::whereId($otpRequestData['token'])->where('is_valid', 1)->with('user')->first();
            // if not found
            if (!$otp) {
                DB::rollBack();
                Log::info("OTP not found");
                return $this->sendResponse(false, "", __("Something Went Wrong"), 404, 4001);
            }
            Log::info("OTP - " . json_encode($otp));
            // check submitted otp
            $matchResponse = OtpFacade::checkValidity($otp, $otpRequestData);

            if (!$matchResponse->success) {
                Log::info("OTP did not match");
                DB::commit();
                return $this->sendResponse(false, $matchResponse->data, $matchResponse->msg, 404, 4001);
            }

            Log::info("Update user with otp verified");
            $userService->otpVerified($otp->user, true);
            $internalCode = ($otp->otp_type !== GlobalType::getOtpType('ForgotPassword')) ? 6001 : 6002;
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            return $this->sendResponse(false, "", __("Something Went Wrong,Please Try Again"), 404, 4001);
        }

        return $this->sendResponse(true, $matchResponse->data, $matchResponse->msg, 200, $internalCode);
    }

    /**
     * resendOtp
     *
     * @param  OtpRequest $otpRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendOtp(OtpRequest $otpRequest)
    {
        DB::beginTransaction();
        try {
            $otpResponse = OtpFacade::regenerateOtp($otpRequest->validated());
            Log::info("OTP regenrate response" . json_encode($otpResponse));

            if ($otpResponse->success) {
                DB::commit();

                return $this->sendResponse(
                    true,
                    $otpResponse->data,
                    $otpResponse->msg
                );
            }

            DB::rollBack();
            return $this->sendResponse(
                false,
                "",
                $otpResponse->msg,
                404,
                4001
            );
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);

            return $this->sendResponse(
                false,
                "",
                "Something went wrong",
                404,
                4001
            );
        }
    }
}
