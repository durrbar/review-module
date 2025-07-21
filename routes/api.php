<?php

use Illuminate\Support\Facades\Route;
use Modules\Review\Http\Controllers\ReviewController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function (): void {
    Route::prefix('{modelType}/{modelId}/review')->name('review.')->group(function (): void {
        Route::apiResource('/', ReviewController::class)->parameters(['' => 'review']);

        // Helpful count update routes
        Route::patch('{review}/helpful/increment', [ReviewController::class, 'incrementHelpful'])->name('helpful.increment');
        Route::patch('{review}/helpful/decrement', [ReviewController::class, 'decrementHelpful'])->name('helpful.decrement');
    });
});
