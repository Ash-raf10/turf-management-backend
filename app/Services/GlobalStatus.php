<?php

namespace App\Services;

class GlobalStatus
{
    private static $recordStatus = [
        'Active' => 'Active',
        'Inactive' => 'Inactive',
        'Deleted' => 'Deleted',
        'Draft' => 'Draft',
        'Completed' => 'Completed',
        'Accepted' => 'Accepted',
        'Rejected' => 'Rejected',
        'Locked' => 'Locked'
    ];

    private static $bookingStatus = [
        'Pending' => 'Pending',
        'Booked' => 'Booked',
        'Rejected' => 'Rejected',
        'Locked' => 'Locked',
        'Failed' => 'Failed'
    ];

    public static function getRecordStatus($key = null): mixed
    {

        return $key ? self::$recordStatus[$key] : self::$recordStatus;
    }

    public static function getBookingStatus($key = null): mixed
    {

        return $key ? self::$bookingStatus[$key] : self::$bookingStatus;
    }
}
