<?php

namespace App\Services\Customer;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CustomerService
{

    public function registerUser(array $requestData): ?UserResource
    {
        $requestData['password'] =  Hash::make($requestData['password']);

        $newUser = User::create($requestData);

        return $newUser ? new UserResource($newUser) : null;
    }
}
