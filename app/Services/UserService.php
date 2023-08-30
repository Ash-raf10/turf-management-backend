<?php

namespace App\Services;

use App\Models\User;
use App\Mail\OtpMail;
use App\Facades\OtpFacade;
use App\Services\Sms\SmsService;
use App\Traits\InternalResponse;
use App\Traits\InternalResponseObject;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserService
{
    use InternalResponse;

    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }


    public function generateOtpForUser(User $user, string $type): InternalResponseObject
    {
        $otpResponse = OtpFacade::generate($user, $type);

        if (!$otpResponse->success) {
            return $this->response(false);
        }

        return $this->response(true, $otpResponse->data);
    }
    /**
     * send otp to user
     *
     * @param  mixed $user
     *
     * @return bool
     */
    public function sendOtp(User $user, array $otpData): void
    {
        if (config('otp.email')) {
            Log::info("Sending OTP in Mail");
            Mail::to($user->email)->send(new OtpMail($user, $otpData));
        }
        if (config('otp.mobile')) {
            Log::info("Sending OTP in Mobile");

            $this->smsService->setSmsTemplate('sms.otp')
                ->setSmsTemplateVariables(['user' => $user, 'otp' => $otpData])
                ->sendSms($user->mobile)->saveSmsInfo();
        }
    }

    /**
     * otp verified true or false
     *
     * @param  User $user
     * @param  bool $isVerified
     * @return void
     */
    public function otpVerified(User $user, bool $isVerified): void
    {
        $user->otp_verified = $isVerified;
        $user->save();
    }
}
