Hello {{ $user->name ?? '' }},
Your OTP is {{ $otp['otp'] }} and it will expire after {{ $otp['expiry_time'] }}
@tms
