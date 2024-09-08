<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Otp;
use App\Facades\OtpFacade;
use App\Traits\ApiResponse;
use App\Services\GlobalType;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Validation\Rule;
use App\Http\Requests\OtpRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class OtpVerificationMiddleware
{

    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       $response = OtpFacade::otpValidateFromMiddleware($request);

       if(!$response->success)
       {
        return $this->sendResponse(false,"",$response->msg,400,0000);
       }
              
        return $next($request);
    }
}
