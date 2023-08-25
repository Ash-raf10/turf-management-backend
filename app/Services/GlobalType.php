<?php

namespace App\Services;

class GlobalType
{
    private static $otpType = [
        'Registration' => 'Registration',
        'ForgotPassword' => 'ForgotPassword',
        'Login' => 'Login'
    ];

    public static function getOtpType($key = null): mixed
    {

        return $key ? self::$otpType[$key] : self::$otpType;
    }
}
