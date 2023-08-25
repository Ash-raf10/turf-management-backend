<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Otp;
use App\Models\User;
use App\Traits\InternalResponseObject;
use App\Traits\InternalResponse;

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
        $otp = [];
        $otp['user_id'] = $user->id;
        // 6 digits otp
        $otp['otp'] = random_int(100000, 999999);
        $otp['expired_at'] = Carbon::now()->addMinutes(config('otp.expiry_time'));
        $otp['otp_type'] = $type;

        $otpRecord = Otp::create($otp);

        if ($otpRecord) {
            $data =  [
                'otp' => $otpRecord->otp,
                'id' => $otpRecord->id,
                'expiry_time' => $otp['expired_at']
            ];
            return $this->response(true, $data);
        } else {
            return $this->response(false);
        }
    }

    public function filterOtpResponse(array $otpResponse): array
    {

        $otpResponse['token'] = $otpResponse['id'];
        unset($otpResponse['otp']);
        unset($otpResponse['id']);

        return $otpResponse;
    }
}
