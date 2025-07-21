<?php

use Illuminate\Support\Facades\Route;
use Modules\Review\Http\Controllers\ReviewController;
use Modules\Role\Enums\Permission;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::apiResource('reviews', ReviewController::class, [
    'only' => ['index', 'show'],
]);

/**
 * ******************************************
 * Authorized Route for Customers only
 * ******************************************
 */
Route::group(['middleware' => ['can:'.Permission::CUSTOMER, 'auth:sanctum', 'email.verified']], function (): void {
    Route::apiResource('reviews', ReviewController::class, [
        'only' => ['store', 'update'],
    ]);
});

/**
 * *****************************************
 * Authorized Route for Super Admin only
 * *****************************************
 */
Route::group(['middleware' => ['permission:'.Permission::SUPER_ADMIN, 'auth:sanctum']], function (): void {
    Route::apiResource('reviews', ReviewController::class, [
        'only' => ['destroy'],
    ]);
});
