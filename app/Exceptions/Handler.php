<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use App\Traits\ApiResponse;
use BadMethodCallException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    use ApiResponse;
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        if (config('app.debug')) {
            return false;
        }
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->wantsJson()) {
                Log::error($e);
                return $this->sendResponse(false, "", "Resource Not Found", 404, 1);
            }
        });
        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->wantsJson()) {
                Log::error($e);
                return $this->sendResponse(false, "", "The specified method for the request is invalid", 405, 2);
            }
        });
        $this->renderable(function (BadRequestHttpException $e, $request) {
            if ($request->wantsJson()) {
                Log::error($e);
                return $this->sendResponse(false, "", "Bad Request", 400, 3);
            }
        });
        $this->renderable(function (BadMethodCallException $e, $request) {
            if ($request->wantsJson()) {
                Log::error($e);
                return $this->sendResponse(false, "", "Bad Method call", 500, 4);
            }
        });
        $this->renderable(function (HttpException $e, $request) {
            if ($request->wantsJson()) {
                Log::error($e);
                return $this->sendResponse(false, "", "Something went wrong, try again", $e->getStatusCode(), 5);
            }
        });
        $this->renderable(function (Exception $e, $request) {
            if ($request->wantsJson()) {
                Log::error($e);
                return $this->sendResponse(false, "", "Something went wrong", 404, 6);
            }
        });
        $this->renderable(function (Throwable $e, $request) {
            if ($request->wantsJson()) {
                Log::error($e);
                return $this->sendResponse(false, "", "Error", 404, 7);
            }
        });
    }
}
