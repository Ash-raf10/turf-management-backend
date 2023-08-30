<?php

namespace App\Services\Sms;

use App\Interface\SmsProviderServiceInterface;
use App\Models\SmsLog;
use App\Models\User;
use App\Traits\InternalResponse;
use Illuminate\Support\Facades\Log;
use App\Traits\InternalResponseObject;
use Exception;

class SmsService
{
    use InternalResponse;

    private string $smsTemplate;
    private array $smsTemplateVariables  = [];
    private InternalResponseObject $smsResponse;
    private string|array $mobileNumber;
    private string $sms;

    public function  __construct(private SmsProviderServiceInterface $smsProviderService)
    {
    }

    /**
     * Set the value of smsTemplateVariables
     *
     * @return  self
     */
    public function setSmsTemplateVariables($smsTemplateVariables)
    {
        $this->smsTemplateVariables = $smsTemplateVariables;

        return $this;
    }

    /**
     * Set the value of smsTemplate
     *
     * @return  self
     */
    public function setSmsTemplate($smsTemplate)
    {
        $this->smsTemplate = $smsTemplate;

        return $this;
    }

    /**
     * Set the value of smsResponse
     *
     * @return  self
     */
    public function setSmsResponse(InternalResponseObject $smsResponse)
    {
        $this->smsResponse = $smsResponse;

        return $this;
    }

    /**
     * Set the value of mobileNumber
     *
     * @return  self
     */
    public function setMobileNumber($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;

        return $this;
    }

    /**
     * Set the value of sms
     *
     * @return  self
     */
    public function setSms($sms)
    {
        $this->sms = $sms;

        return $this;
    }

    public function sendSms(string|array $mobileNumber, ?string $msg = null): self
    {
        $msg = $msg ?? $this->makeSmsTemplate();
        Log::info($msg);
        $smsResponse =  $this->smsProviderService->sendSimpleMessage($mobileNumber, $msg);
        Log::info("Sms Response", ["response" => $smsResponse]);

        $this->setSmsResponse($smsResponse);
        $this->setMobileNumber($mobileNumber);
        $this->setSms($msg);

        return $this;
    }

    public function makeSmsTemplate(): string
    {
        return view($this->smsTemplate, $this->smsTemplateVariables)->render();
    }

    public function saveSmsInfo(): array
    {
        if (!$this->smsResponse->success) {
            return false;
        }

        if (is_array($this->mobileNumber)) {
            $usersIdMobileArray = User::whereIn('mobile', $this->mobileNumber)->pluck('mobile', 'id')->toArray();
        } else {
            $usersIdMobileArray = User::where('mobile', $this->mobileNumber)->pluck('mobile', 'id')->toArray();
        }
        Log::info("User ID and Mobile", $usersIdMobileArray);

        $smsLog = [];
        foreach ($usersIdMobileArray as $userId => $mobile) {
            $smsLog[$mobile] = $this->saveSmsLog($userId, $mobile);
        }

        return $smsLog;
    }

    public function saveSmsLog(string $id, string $mobile): SmsLog
    {
        $data['user_id'] = $id;
        $data['mobile'] = $mobile;
        $data['sms_id'] = $this->smsResponse->data->sms_id;
        $data['sms'] = $this->sms;
        $data['response'] =  json_encode($this->smsResponse->data);
        Log::info("Saving sms log- ", $data);

        return SmsLog::create($data);
    }
}
