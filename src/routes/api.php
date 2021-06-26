<?php

use App\Http\Controllers\Api\V1\BatchController;
use App\Libraries\Response;
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

Route::prefix('v1')->middleware('auth:api')->group(function () {

    Route::get('user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('batches')->middleware('throttle:60,1')->group(function () {
        Route::get('{uuid}', [BatchController::class, 'show'])->name('show');
        Route::post('', [BatchController::class, 'store']);
        #Route::get('', [BatchController::class, 'index']);
    });

});

Route::fallback(function () {
    return Response::json(404, [], null, 'Page Not Found. If error persists, contact info@domain.com');
});