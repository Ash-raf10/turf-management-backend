<?php

namespace App\Interface;

use App\Traits\InternalResponseObject;

interface SmsProviderServiceInterface
{
    /**
     * sendSimpleMessage
     *
     * @param  string|array $mobileNumbers
     * @param  string $message
     * @return InternalResponseObject
     */
    public function sendSimpleMessage(string|array $mobileNumbers, string $message): InternalResponseObject;
}
