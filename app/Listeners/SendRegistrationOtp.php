<?php

namespace App\Listeners;

use App\Mail\OtpMail;
use App\Events\UserCreated;
use App\Services\OtpService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendRegistrationOtp
{

    public $afterCommit = true;
    /**
     * The number of times the queued listener may be attempted.
     *
     * @var int
     */
    public $tries = 2;
    /**
     * Create the event listener.
     */
    public function __construct(private OtpService $otpService)
    {
        // ...
    }

    /**
     * Handle the event.
     */
    public function handle(UserCreated $event): void
    {
        // Access the user using $event->user...
        Log::info("User Created Observer", $event->user->toArray());
        $otpResponse = $this->otpService->generate($event->user);

        if (!$otpResponse->success) {
            Log::info("Otp generation Failed", json_encode($otpResponse));

            return;
        }

        if (config('otp.email')) {
            Log::info("Sending OTP in Mail");
            Mail::to($event->user->email)->send(new OtpMail($event->user, $otpResponse->data));
        }
        if (config('otp.mobile')) {
            Log::info("Sending OTP in Mobile");
            //integrate mobile otp service
        }
    }
}
