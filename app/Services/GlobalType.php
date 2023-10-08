<?php

namespace App\Services;

class GlobalType
{
    private static $otpType = [
        'Registration' => 'Registration',
        'ForgotPassword' => 'ForgotPassword',
        'Login' => 'Login'
    ];

    private static $fieldType = [
        'Football' => 'Football',
        'Cricket' => 'Cricket',
        'Table Tennis' => 'Table Tennis',
        'Badmintoon' => 'Badmintoon'
    ];

    public static function getOtpType($key = null): mixed
    {

        return $key ? self::$otpType[$key] : self::$otpType;
    }

    public static function getFieldType($key = null): mixed
    {

        return $key ? self::$fieldType[$key] : self::$fieldType;
    }
}
