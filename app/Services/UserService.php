<?php

namespace App\Services;

use App\Models\User;
use App\Mail\OtpMail;
use App\Facades\OtpFacade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserService
{
    /**
     * send otp to user
     *
     * @param  mixed $user
     *
     * @return bool
     */
    public function sendOtp(User $user): bool
    {
        $otpResponse = OtpFacade::generate($user);

        if (!$otpResponse->success) {
            Log::info("Otp generation Failed", json_encode($otpResponse));

            return false;
        }

        if (config('otp.email')) {
            Log::info("Sending OTP in Mail");
            Mail::to($user->email)->send(new OtpMail($user, $otpResponse->data));
        }
        if (config('otp.mobile')) {
            Log::info("Sending OTP in Mobile");
            //integrate mobile otp service
        }

        return true;
    }
}
