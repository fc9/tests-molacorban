<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Phpro\ApiProblem\Http\NotFoundProblem;

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

Route::prefix('v1')->middleware('auth:api')->group(function () {

    Route::get('user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('batches')->middleware('throttle:60,1')->group(function () {
        Route::get('{uuid}', [\App\Http\Controllers\Api\V1\BatchController::class, 'show'])->name('show');
        Route::post('', [\App\Http\Controllers\Api\V1\BatchController::class, 'store']);
        #Route::get('', [\App\Http\Controllers\Api\V1\BatchController::class, 'index']);
    });

});

Route::fallback(function () {
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact info@domain.com',
        'status' => false
    ], 404);
});