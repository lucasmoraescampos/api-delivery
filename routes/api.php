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

Route::get('categories', 'ApiController@categories');

Route::get('category/{category_id}/plans', 'ApiController@plans');

Route::get('companies-by-all-categories', 'ApiController@companiesByAllCategories');

Route::get('companies-by-category', 'ApiController@companiesByCategory');

Route::get('search-companies', 'ApiController@searchCompanies');

Route::get('search-products', 'ApiController@searchProducts');

Route::get('company/{slug}', 'ApiController@company');

Route::get('product/{id}', 'ApiController@product');

Route::get('payment-methods', 'ApiController@paymentMethods');

Route::post('check-duplicity', 'ApiController@checkDuplicity');

Route::post('send-code-verification', 'ApiController@sendCodeVerification');

Route::post('confirm-code-verification', 'ApiController@confirmCodeVerification');

Route::namespace('User')->prefix('user')->group(function () {

    Route::post('sign-up', 'AuthController@signUp');

    Route::post('authenticate', 'AuthController@authenticate');

    Route::post('authenticate-with-provider', 'AuthController@authenticateWithProvider');

    Route::post('check-in-fcm-token-without-auth', 'AuthController@checkInfcmToken');

    Route::middleware(['auth:users'])->group(function () {

        Route::get('auth', 'AuthController@auth');

        Route::post('logout', 'AuthController@logout');

        Route::post('check-in-fcm-token-with-auth', 'AuthController@checkInfcmToken');


        Route::get('card', 'CardController@index');

        Route::get('card/{id}', 'CardController@show');

        Route::post('card', 'CardController@store');


        Route::get('location', 'LocationController@index');

        Route::post('location', 'LocationController@store');

        Route::put('location/{id}', 'LocationController@update');

        Route::delete('location/{id}', 'LocationController@delete');

        
        Route::post('company', 'CompanyController@store');

        Route::put('company/{id}', 'CompanyController@update');

        Route::delete('company/{id}', 'CompanyController@delete');


        Route::get('order', 'OrderController@index');

        Route::post('order', 'OrderController@store');

    });

});

Route::namespace('Company')->prefix('company')->group(function () {

    Route::middleware(['auth:users', 'checkCompany'])->group(function () {

        Route::get('{company_id}/segment', 'SegmentController@index');

        Route::post('{company_id}/segment', 'SegmentController@store');

        Route::post('{company_id}/segment/reorder', 'SegmentController@reorder');

        Route::put('{company_id}/segment/{id}', 'SegmentController@update');

        Route::delete('{company_id}/segment/{id}', 'SegmentController@delete');


        Route::get('{company_id}/product', 'ProductController@index');

        Route::post('{company_id}/product', 'ProductController@store');

        Route::put('{company_id}/product/{id}', 'ProductController@update');

        Route::delete('{company_id}/product/{id}', 'ProductController@delete');


        Route::post('{company_id}/complement', 'ComplementController@store');

        Route::post('{company_id}/complement/{id}', 'ComplementController@update');

        Route::delete('{company_id}/complement/{id}', 'ComplementController@delete');

        
        Route::post('{company_id}/subcomplement', 'SubcomplementController@store');

        Route::post('{company_id}/subcomplement/{id}', 'SubcomplementController@update');

        Route::delete('{company_id}/subcomplement/{id}', 'SubcomplementController@delete');


        Route::get('{company_id}/order', 'OrderController@index');

        Route::put('{company_id}/order/{id}', 'OrderController@update');


        Route::get('{company_id}/deliveryman', 'DeliverymanController@index');

        Route::post('{company_id}/deliveryman', 'DeliverymanController@store');

        Route::delete('{company_id}/deliveryman/{id}', 'DeliverymanController@delete');

    });
    
});

Route::prefix('admin')->group(function () {

    Route::middleware(['auth:users', 'checkAdmin'])->group(function () {

        Route::put('company/{id}', 'Admin\CompanyController@update');

    });

});