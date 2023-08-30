<?php

namespace App\Services\Sms;

use App\Interface\SmsProviderServiceInterface;
use App\Traits\InternalResponse;
use App\Traits\InternalResponseObject;
use Exception;
use Illuminate\Support\Facades\Log;

class SmsProviderService implements SmsProviderServiceInterface
{
    use InternalResponse;

    private $baseUrl;
    private $customerId;
    private $apiKey;
    private $apiUrl;

    public function __construct()
    {
        $this->baseUrl = config('smsprovider.base_url');
        $this->customerId = config('smsprovider.customer_id');
        $this->apiKey = config('smsprovider.api_key');
    }

    /**
     * sendSimpleMessage
     *
     * @param  string|array $mobileNumbers
     * @param  string $message
     * @return InternalResponseObject
     */
    public function sendSimpleMessage(string|array $mobileNumbers, string $message): InternalResponseObject
    {
        $this->setApiUrl(config('smsprovider.simple_sms_url'));

        $smsData['mobile_no'] = is_array($mobileNumbers) ? implode(',', $mobileNumbers) : $mobileNumbers;
        $smsData['message'] = $message;

        return $this->buildApiRequest($this->getApiUrl(), $smsData);
    }

    /**
     * check sms balance
     *
     * @return InternalResponseObject
     */
    public function checkBalance(): InternalResponseObject
    {
        $this->setApiUrl(config('smsprovider.balance_url'));

        return $this->buildApiRequest($this->getApiUrl());
    }

    /**
     * smsReport
     *
     * @param  string|array $smsIds
     * @return InternalResponseObject
     */
    public function smsReport(string|array $smsIds): InternalResponseObject
    {
        $this->setApiUrl(config('smsprovider.sms_report'));

        $smsData['sms_ids'] = is_array($smsIds) ? implode(',', $smsIds) : $smsIds;

        return $this->buildApiRequest($this->getApiUrl(), $smsData);
    }

    /**
     * buildApiRequest
     *
     * @param  string $url
     * @param  array $smsData
     * @return InternalResponseObject
     */
    public function buildApiRequest($url, $smsData = []): InternalResponseObject
    {
        if (!$url) {
            return false;
        }

        $smsData['customer_id'] = $this->customerId;
        $smsData['api_key'] = $this->apiKey;
        Log::info("SMS DATA", $smsData);

        $headers = ['content-type' => 'application/json', 'accept' => 'application/json'];
        $output = false;
        try {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $smsData);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            Log::info("CURL REQUEST- " . json_encode($curl));

            $output = curl_exec($curl);
            Log::info("Sent curl request");
            Log::info("CURL REQUEST- " . json_encode($curl));
            Log::info("OUTPUT- $output");
            // Check if an error occurred
            if ($output === false) {
                throw new Exception(curl_error($curl));
            }
        } catch (\Exception $exception) {
            Log::info("in exception");
            Log::error($exception);

            curl_close($curl);

            return $this->response(false);
        }

        Log::info("returning output");
        $output = json_decode($output);
        if ($output->status === 'Failed') {
            return $this->response(false);
        }
        curl_close($curl);
        return $this->response(true, $output);
    }

    /**
     * getApiUrl
     *
     * @return string
     */
    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     * setApiUrl
     *
     * @param  string $apiUrl
     * @return void
     */
    public function setApiUrl($apiUrl): void
    {
        $this->apiUrl = $this->baseUrl . $apiUrl;
    }
}
