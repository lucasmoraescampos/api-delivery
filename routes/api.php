<?php

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

Route::get('company/{slug}', 'ApiController@company');

Route::get('states', 'ApiController@states');

Route::get('cities/{uf}', 'ApiController@cities');

Route::get('payment-methods', 'ApiController@paymentMethods');

Route::get('online-payment-fee', 'ApiController@onlinePaymentFee');

Route::post('check-duplicity', 'ApiController@checkDuplicity');

Route::post('send-code-verification', 'ApiController@sendCodeVerification');

Route::post('confirm-code-verification', 'ApiController@confirmCodeVerification');

Route::namespace('User')->prefix('user')->group(function () {

    Route::get('plan', 'PlanController@index');

    Route::post('sign-up', 'AuthController@signUp');

    Route::post('authenticate', 'AuthController@authenticate');

    Route::post('authenticate-with-provider', 'AuthController@authenticateWithProvider');

    Route::middleware(['auth:users'])->group(function () {

        Route::post('logout', 'AuthController@logout');

        Route::post('plan', 'PlanController@store');

        Route::post('card', 'CardController@store');

        Route::post('company', 'CompanyController@store');
    });

});

Route::namespace('Company')->prefix('company')->group(function () {

    Route::middleware(['auth:users'])->group(function () {

        Route::get('{company_id}/segment', 'SegmentController@index');

        Route::post('{company_id}/segment', 'SegmentController@store');

        Route::post('{company_id}/segment/reorder', 'SegmentController@reorder');

        Route::put('{company_id}/segment/{id}', 'SegmentController@update');

        Route::delete('{company_id}/segment/{id}', 'SegmentController@delete');


        Route::get('{company_id}/product', 'ProductController@index');

        Route::post('{company_id}/product', 'ProductController@store');

        Route::put('{company_id}/product/{id}', 'ProductController@update');

        Route::delete('{company_id}/product/{id}', 'ProductController@delete');


        Route::get('{company_id}/delivery-person', 'DeliveryPersonController@index');

        Route::post('{company_id}/delivery-person', 'DeliveryPersonController@store');

        Route::put('{company_id}/delivery-person/{id}', 'DeliveryPersonController@update');

        Route::delete('{company_id}/delivery-person/{id}', 'DeliveryPersonController@delete');


        Route::get('{company_id}/order', 'OrderController@index');

        Route::post('{company_id}/order', 'OrderController@store');
    });
    
});
