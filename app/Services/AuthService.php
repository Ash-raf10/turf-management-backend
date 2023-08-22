<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

class AuthService
{

    /**
     * login user with email and password
     *
     * @param  array $credentials['email','password]
     * @return array<false|string,UserResource|null>
     */
    public function login(array $credentials): array
    {
        // for login with either mobile or email
        $modifiedCredentials[$credentials['identifier_type']] = $credentials['email_phone'];
        $modifiedCredentials['password'] = $credentials['password'];

        $token = Auth::attempt($modifiedCredentials);
        $user = $this->getAuthUser();

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
}