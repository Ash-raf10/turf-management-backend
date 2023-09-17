<?php

namespace App\Services;

use App\Models\User;
use App\Http\Resources\UserResource;
use App\Models\SsoUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\InternalResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService
{
    use InternalResponse;

    /**
     * login user with email and password
     *
     * @param  array $credentials['email','password]
     * @return array<false|string,User|null>
     */
    public function login(array $credentials): array
    {
        // for login with either mobile or email
        $modifiedCredentials[$credentials['identifier_type']] = $credentials['email_phone'];
        $modifiedCredentials['password'] = $credentials['password'];

        $token = Auth::attempt($modifiedCredentials);
        $user = Auth::user();

        return [$token, $user];
    }

    /**
     * Get the authenticated User
     * @return UserResource|null
     */
    public function getAuthUser(): ?UserResource
    {
        $authUser = Auth::user();

        return $authUser ? new UserResource($authUser) : null;
    }

    /** generate refresh token for a user
     *  return the token
     *  @return ?string
     */
    public function refreshToken(): ?string
    {
        return Auth::refresh();
    }

    /**
     * generateTokenForUser
     *
     * @param  User $user
     * @return ?string
     */
    public function generateTokenForUser(User $user)
    {
        return Auth::fromUser($user);
    }


    public function ssoUserRegister(array $requestData): array
    {
        $user = User::where('email',$requestData['email'])->first();
        if(!$user){
            //registration
            DB::beginTransaction();
            $newRequest['name'] = $requestData['name'];
            $newRequest['email'] = $requestData['email'];
            $newRequest['password'] =  Hash::make($requestData['social_id']);
            $newRequest['otp_verified'] =  1;

            $user = User::create($newRequest);

            Log::info("Created User");
            Log::info(json_encode($user));
            try {
                $requestData['user_id'] = $user['id'];
                $newSsoUser = SsoUser::create($requestData);
                DB::commit();
                Log::info("SsoUser created");
            } catch (\Exception $ex) {
                Log::error($ex);
                DB::rollBack();
            }

        }

        //login 
        $token = $this->generateTokenForUser($user);
        
        return [$token, $user];
    }
}
