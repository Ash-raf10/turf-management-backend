<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Customer\CustomerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/




Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
});

Route::middleware('auth.jwt')->controller(AuthController::class)->group(function () {
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::get('me', 'me');
});

Route::prefix('customer')->controller(CustomerController::class)->group(function () {
    Route::post('register', 'register');
});

Route::fallback(function () {
    $response = [
        'success' => false,
        'data'    => "",
        'message' => "Route Not Found",
        'code' => 99
    ];

    return response()->json($response, 404);
});
