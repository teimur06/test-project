<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DeliveryRequestController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\LoginController;

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

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware(['auth:api'])->group(function (){
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::group(['prefix' => 'delivery'], function () {
        Route::get('/', [DeliveryRequestController::class, 'index'])->name('delivery.index');
        Route::post('/', [DeliveryRequestController::class, 'store'])->name('delivery.store');
        Route::put('/{id}', [DeliveryRequestController::class, 'update'])->name('delivery.update');
        Route::middleware(['admin'])->group(function () {
            Route::delete('/{id}', [DeliveryRequestController::class, 'destroy'])->name('delivery.destroy');
        });
    });

    Route::group(['prefix' => 'cities'], function () {
        Route::get('/', [CityController::class, 'index'])->name('cities.index');
        Route::middleware(['admin'])->group(function () {
            Route::post('/', [CityController::class, 'store'])->name('cities.story');
            Route::delete('/{id}', [CityController::class, 'destroy'])->name('cities.destroy');
        });
    });

    Route::group(['prefix' => 'users'],function () {
        Route::middleware(['admin'])->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::post('/update_is_admin', [UserController::class, 'updateIsAdmin']);
        });
    });
});



//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
