<x-mail::message>
    ### Dear {{ $user->name }},
    Thank you for registering in our TMS Service.
    Your OTP is {{ $otp['otp'] }} and it will expire after {{ $otp['expiry_time'] }}

    Thanks,
    {{ config('app.name') }}
</x-mail::message>
