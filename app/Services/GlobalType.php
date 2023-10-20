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

    private static $documentType = [
        'turf' => [
            'model' => 'App\Models\Turf',
            'file_path' => 'TURF',
            'max_count' => 5
        ],
        'field' => [
            'model' => 'App\Models\Field',
            'file_path' => 'FIELD',
            'max_count' => 5
        ],
    ];

    public static function getOtpType($key = null): mixed
    {

        return $key ? self::$otpType[$key] : self::$otpType;
    }

    public static function getFieldType($key = null): mixed
    {

        return $key ? self::$fieldType[$key] : self::$fieldType;
    }

    public static function getDocumentType($key = null): mixed
    {

        return $key ? self::$documentType[$key] : self::$documentType;
    }
}
