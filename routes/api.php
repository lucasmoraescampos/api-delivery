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

Route::get('product/{photo}', 'Company\ProductController@showPhoto')->name('productPhoto');

Route::prefix('user')->group(function () {

    Route::post('register', 'User\AuthController@store');

    Route::prefix('verify')->group(function () {

        Route::post('email', 'User\VerifyController@storeEmail');

        Route::post('phone', 'User\VerifyController@storePhone');

    });

    Route::group(['middleware' => 'assign.guard:users'], function () {


        Route::group(['middleware' => 'auth.jwt'], function () {


        });


    });

});

Route::prefix('company')->group(function () {

    Route::get('categories', 'Company\AuthController@showCategories');

    Route::post('register', 'Company\AuthController@store');

    Route::post('login', 'Company\AuthController@login');

    Route::group(['middleware' => ['assign.guard:companies', 'auth.jwt']], function () {

        Route::prefix('menusession')->group(function () {

            Route::get('/', 'Company\MenuSessionController@index');

            Route::get('/{id}', 'Company\MenuSessionController@show');

            Route::post('/', 'Company\MenuSessionController@store');

            Route::put('/{id}', 'Company\MenuSessionController@update');

            Route::delete('/{id}', 'Company\MenuSessionController@delete');

        });

        Route::prefix('subcategory')->group(function () {

            Route::get('/', 'Company\SubcategoryController@index');

        });

        Route::prefix('product')->group(function () {

            Route::get('/', 'Company\ProductController@index');

            Route::get('{id}', 'Company\ProductController@show');

            Route::post('/', 'Company\ProductController@store');

            Route::post('complement', 'Company\ProductController@storeComplement');

            Route::post('subcomplement', 'Company\ProductController@storeSubcomplement');

            Route::post('photo', 'Company\ProductController@storePhoto');

            Route::put('{id}', 'Company\ProductController@update');

            Route::put('complement/{id}', 'Company\ProductController@updateComplement');

            Route::put('subcomplement/{id}', 'Company\ProductController@updateSubcomplement');

            Route::put('status/{id}', 'Company\ProductController@updateStatus');

            Route::delete('{id}', 'Company\ProductController@delete');

            Route::delete('complement/{id}', 'Company\ProductController@deleteComplement');

            Route::delete('subcomplement/{id}', 'Company\ProductController@deleteSubcomplement');

        });

    });

});
