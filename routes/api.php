<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\AuthController;
use \App\Http\Controllers\PermissionController;
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
Route::prefix('v1')->group(function () {

    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:api')->post('logout', [AuthController::class, 'logout']);
    Route::middleware('auth:api')->post('refresh', [AuthController::class, 'refresh']);
    Route::post('register', [AuthController::class, 'register']);
    Route::prefix('dashboard')->group(function () {
        Route::middleware('role:admin')->group(function () {
            Route::get('', function () {
                return 'Welcome Admin';
            });
        });
    });
    Route::middleware('role:user')->get('dashboard',function() {
        return 'Welcome User';
    });

    Route::fallback(function () {
        return response()->json(['error' => 'Not Found!'], 404);
    });
});



