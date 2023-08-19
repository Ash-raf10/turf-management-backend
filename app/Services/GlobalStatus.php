<?php

namespace App\Services;

class GlobalStatus
{
    private static $recordStatus = [
        'Active' => 'Active',
        'Inactive' => 'Inactive',
        'Draft' => 'Draft',
        'Completed' => 'Completed',
        'Accepted' => 'Accepted',
        'Rejected' => 'Rejected',
        'Locked' => 'Locked'
    ];

    public static function getRecordStatus($key = null): mixed
    {

        return $key ? self::$recordStatus[$key] : self::$recordStatus;
    }
}
