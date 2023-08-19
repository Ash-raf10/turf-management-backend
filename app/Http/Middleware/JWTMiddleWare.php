<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;

/**
 * AuthController
 *
 * @category Class
 * @author   Ashraf <shahdatashraf@10gmail.com>
 */
class JWTMiddleWare
{
    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param Request $request Request Object
     * @param \Closure(Request): (Response|RedirectResponse) $next
     *
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $refreshed = null;
        $tokenRefreshed = false;
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                // if the token is invalid
                $statusCode = 401;
                $statusMsg = "This token is invalid. Please Login";
            } elseif ($e instanceof TokenExpiredException) {
                // If the token is expired, then it will be refreshed and added to the headers
                try {
                    $refreshed = JWTAuth::refresh(JWTAuth::getToken());
                    JWTAuth::setToken($refreshed)->toUser();
                    $request->headers->set('Authorization', 'Bearer ' . $refreshed);

                    $tokenRefreshed = true;
                } catch (JWTException $e) {
                    // for any other exception related to jwt
                    $statusCode = 403;
                    $statusMsg = "Token can not be refreshed.Please login again.";
                }
            } else {

                $statusCode = 404;
                $statusMsg = "Authorization Token not found.";
            }

            if (!$tokenRefreshed) {
                return $this->sendResponse(false, "", $statusMsg, $statusCode, 6000);
            }
        }
        $response = $next($request);
        if ($refreshed) {
            $response->header('Authorization', 'Bearer ' . $refreshed);
        }

        return $response;
    }
}
