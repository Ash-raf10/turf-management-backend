<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;

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
    public function __construct(private UserService $userService)
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
        $result = $this->userService->sendOtp($event->user);
        Log::info("Sending OTP Success - $result");
    }
}
