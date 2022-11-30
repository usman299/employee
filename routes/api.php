<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');
Route::post('send/otp', 'API\UserController@sendOtpEmail');
Route::post('/otp/verify', 'API\UserController@otpVerifyEmail');
Route::post('/forget/password', 'API\UserController@forgetPassword');

Route::group(['middleware' => 'auth:api'], function(){
    Route::post('details', 'API\UserController@details');
    Route::post('/profile/update', 'API\UserController@update');
    Route::post('/password/update', 'API\UserController@passwordUpdate');
    Route::post('/profile/image/update', 'API\UserController@profileImage');
    Route::get('employee', 'API\UserController@employee');
    Route::get('employee/{id}', 'API\UserController@singleEmployee');
    Route::post('employee/search', 'API\UserController@employeeSearch');
    Route::post('/logout', 'API\UserController@logout');
});
