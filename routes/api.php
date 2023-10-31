<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\OtpController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Company\RoleController;
use App\Http\Controllers\Api\V1\Company\TurfController;
use App\Http\Controllers\Api\V1\Company\FieldController;
use App\Http\Controllers\Api\V1\Company\CompanyController;
use App\Http\Controllers\Api\V1\Customer\CustomerController;
use App\Http\Controllers\Api\V1\Documents\ImageController;
use App\Http\Controllers\Api\V1\Company\SlotController;
use App\Http\Controllers\Api\V1\Customer\SlotSearchController;

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


Route::controller(AuthController::class)->group(function () {
    Route::post('sociallogin', 'sociallogin');
});



Route::controller(OtpController::class)->group(function () {
    Route::post('otp', 'matchOtp');
    Route::post('otp/resend', 'resendOtp');
});

Route::middleware('auth.jwt')->controller(AuthController::class)->group(function () {
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::get('me', 'me');
});

Route::prefix('customer')->controller(CustomerController::class)->group(function () {
    Route::post('register', 'register');
});
Route::prefix('company')->controller(CompanyController::class)->group(function () {
    Route::post('register', 'register');
});

Route::prefix('company')->middleware('auth.jwt')->group(function () {
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('turfs', TurfController::class);
    Route::apiResource('turfs.fields', FieldController::class)->shallow();
    Route::apiResource('fields.slots', SlotController::class)->shallow()->except('update');
    Route::put('/fields/{field}/slots', [SlotController::class, 'update']);
});

Route::post('slot/search',  [SlotSearchController::class, 'search']);

Route::middleware('auth.jwt')->group(function () {
    Route::apiResource('documents/images', ImageController::class)->only('store', 'destroy');
});



// Route::prefix('company')->middleware('auth.jwt')->group(function () {
//     Route::prefix('slot')->controller(SlotController::class)->group(function () {
//         Route::get('',  'index');
//         Route::post('save', 'save');
//         Route::put('update', 'update');
//         Route::delete('{slot}',  'delete');
//         Route::post('search',  'search');
//         Route::post('book',  'book');
//     });
// });

Route::fallback(function () {
    $response = [
        'success' => false,
        'data'    => "",
        'message' => "Route Not Found",
        'code' => 99
    ];

    return response()->json($response, 404);
});
