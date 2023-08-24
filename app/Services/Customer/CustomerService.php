<?php

namespace App\Services\Customer;

use App\Models\User;
use App\Events\UserCreated;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;

class CustomerService
{

    public function registerUser(array $requestData): ?UserResource
    {
        $requestData['password'] =  Hash::make($requestData['password']);

        $newUser = User::create($requestData);

        Log::info("Created User");
        Log::info(json_encode($newUser));
        Log::info("Dispatch User Created Event, Send OTP");

        UserCreated::dispatch($newUser);

        return $newUser ? new UserResource($newUser) : null;
    }
}
