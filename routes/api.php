<?php

use App\Http\Controllers\Api\V1\BeerController;
use App\Http\Controllers\Api\V1\CityController;
use App\Http\Controllers\Api\V1\CountryController;
use App\Http\Controllers\Api\V1\PubController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');



Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        //login and register functions
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        //anything that needs to be authenticated
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/session', [AuthController::class, 'session']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });

    });



    //countries routes
    Route::get('/countries', [CountryController::class, 'getCountryList']);



    //city routes
    Route::prefix('cities')->group(function () {
        Route::get('/', [CityController::class, 'getCityList']);

//        Route::get('/{country}', [CityController::class, 'cityByCountry']);
        Route::get('/{country}', [CityController::class, 'getCities']);
    });


    //pub routes
    Route::prefix('pubs')->group(function () {
        Route::get('/', [PubController::class, 'index']);
        Route::get('/city/{city}', [PubController::class, 'getPubByCity']);
        Route::get('/country/{country}', [PubController::class, 'getMainPubContent']);
        Route::get('/pub/{slug}', [PubController::class, 'getPubBySlug']);


        // generate a slug for pubs TODO: needs string update
        Route::get('/slug', [PubController::class, 'generateSlug']);
    });

    Route::prefix('beers')->group(function () {
        Route::get('/', [BeerController::class, 'getBeerList']);
    });


});
