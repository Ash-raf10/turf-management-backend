<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Otp;
use App\Models\User;
use App\Services\Sms\SmsService;
use App\Traits\InternalResponse;
use Illuminate\Support\Facades\Log;
use App\Traits\InternalResponseObject;
use App\Services\Sms\SmsProviderService;

class OtpService
{
    use InternalResponse;
    /**
     * generate
     *
     * @param  mixed $user
     *
     * @return InternalResponseObject
     */
    public function generate(User $user, string $type): InternalResponseObject
    {
        Log::info("Check OTP limit");
        $otpLimitResponse = $this->checkOtpGenerationLimit($user);
        Log::info("OTP limit response- " . json_encode($otpLimitResponse));
        if (!$otpLimitResponse) {
            return $this->response(false, "", __("You have exceeded OTP generation limit for a day"));
        }

        $this->invalidatePreviousOtps($user);

        $otp = [];
        $otp['user_id'] = $user->id;
        // 6 digits otp
        $otp['otp'] = random_int(100000, 999999);
        $otp['expired_at'] = Carbon::now()->addMinutes(config('otp.expiry_time'));
        $otp['otp_type'] = $type;

        $otpRecord = Otp::create($otp);
        Log::info("OTP record- " . json_encode($otpRecord));

        if ($otpRecord) {
            $data =  [
                'otp' => $otpRecord->otp,
                'id' => $otpRecord->id,
                'expiry_time' => $otp['expired_at']
            ];
            return $this->response(true, $data, __("OTP sent successfully"));
        } else {
            return $this->response(false, "", __("OTP generation failed"));
        }
    }


    /**
     * make the previous otp invalid
     *
     * @param  User $user
     * @return void
     */
    public function invalidatePreviousOtps(User $user): void
    {
        Otp::where('user_id', $user->id)->update(['is_valid' => 0]);
    }

    /**
     * check otp generation limit so that user can not
     * request more than 5 otp per day
     *
     * @param  User $user
     * @return bool
     */
    public function checkOtpGenerationLimit(User $user): bool
    {
        $userOtps =  Otp::where('user_id', $user->id)->whereDate('created_at', Carbon::today())->get();
        Log::info("User OTPS- ", $userOtps->toArray());

        if ($userOtps->count() > 5) {
            return false;
        }
        return true;
    }

    /**
     * filter out the otp response
     *
     * @param  array $otpResponse
     * @return array
     */
    public function filterOtpResponse(array $otpResponse): array
    {

        $otpResponse['token'] = $otpResponse['id'];
        unset($otpResponse['otp']);
        unset($otpResponse['id']);

        return $otpResponse;
    }
    /**
     * checkValidity
     *
     * @param  Otp $otp
     * @param  array $otpRequest
     * @return InternalResponseObject
     */
    public function checkValidity(Otp $otp, array $otpRequest): InternalResponseObject
    {
        $data = [];
        $success = false;
        $msg = "";
        if ($otp->expired_at < now()) {
            $msg = __("Otp expired, please click on resend otp");
            $otp->is_valid = 0;
        } elseif ($otp->attempt >= 3) {
            $msg = __("Too many attempts, please click on resend otp");
        } elseif ($otp->otp != $otpRequest['otp']) {
            $msg = __("Otp does not match, please try again");
            $otp->attempt = $otp->attempt + 1;
        } elseif ($otp->otp == $otpRequest['otp']) {
            $msg = ($otp->otp_type !== GlobalType::getOtpType('ForgotPassword')) ?
                __("Otp matched, Please Log in") : __("Otp macthed, You can change your password");
            $success = true;
            $otp->is_valid = 0;
            $otp->verified_at = now();
            $data = ["expiry_time" => $otp->expired_at, "token" => $otp->id];
        }

        Log::info($msg);
        Log::info(json_encode($data));

        $otp->save();

        return $this->response($success, $data, $msg);
    }


    /**
     * regenerateOtp
     *
     * @param  array $otpRequest
     * @return InternalResponseObject
     */
    public function regenerateOtp(array $otpRequest): InternalResponseObject
    {
        $otp = Otp::where(
            'id',
            '=',
            $otpRequest['token']
        )->whereNull('verified_at')->with('user')->first();
        Log::info("Previous OTP- " . json_encode($otp));

        if (!$otp) {
            return $this->response(false, "", __("Something went Wrong"));
        }

        $otpResponse = $this->generate($otp->user, $otp->otp_type);
        Log::info("New generated OTP response- " . json_encode($otpResponse));

        if (!$otpResponse->success) {
            return $this->response(false, "", $otpResponse->msg);
        }
        $resendOtpResponse =  $this->resendOtp($otp, $otpResponse);
        Log::info("Send OTP response- " . json_encode($resendOtpResponse));

        return $this->response(true, $resendOtpResponse->data, $resendOtpResponse->msg);
    }


    /**
     * resendOtp
     *
     * @param  Otp $otp
     * @param  InternalResponseObject $otpResponse
     * @return InternalResponseObject
     */
    public function resendOtp(Otp $otp, InternalResponseObject $otpResponse): InternalResponseObject
    {
        $smsService = new SmsService(new SmsProviderService);
        $userService = new UserService($smsService);
        // make current otp invalid
        $otp->is_valid = 0;
        $otp->save();

        $userService->sendOtp($otp->user, $otpResponse->data);
        $msg = $otpResponse->msg;
        $otpResponse = $this->filterOtpResponse($otpResponse->data);

        return $this->response(true, $otpResponse, $msg);
    }
}
