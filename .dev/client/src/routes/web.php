<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Http,
    Route
};
use Illuminate\Support\Str;

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

Route::get('/', function () {
    return view('welcome');
});

//Route::get('prepare-to-login', function () {
//    $state = Str::random(40);
//
//    session(['state' => $state]);
//
//    $query = http_build_query([
//        'client_id' => env('CLIENT_ID'),
//        'redirect_url' => env('REDIRECT_URL'),
//        'response_type' => 'code',
//        'scope' => '',
//        'state' => $state,
//    ]);
//
//    return redirect(env('API_URL') . 'oauth/authorize?' . $query);
//})->name('prepare.login');

//Route::get('callback', function (Request $request) {
//    //TODO: verificacao de state
//
//    $response = Http::post(env('API_URL') . 'oauth/token', [
//        'grant_type' => 'authorization_code',
//        'client_id' => env('CLIENT_ID'),
//        'client_secret' => env('CLIENT_SECRET'),
//        'redirect_url' => env('REDIRECT_URL'),
//        'code' => $request->code,
//    ]);
//
//    dd($response->json());
//});

Route::get('grant-password', function () {
    $passportRequest = Http::post(env('API_URL') . 'oauth/token', [
        'grant_type' => 'password',
        'client_id' => env('PASSPORT_CLIENT_ID'),
        'client_secret' => env('PASSPORT_CLIENT_SECRET') . 'd',
        'username' => env('USERNAME'),
        'password' => env('PASSWORD'),
        'scope' => ''
    ]);

    //9
    //lEXqs8jrhz0Ls2KjR6pxeuc34vu64U0XsmMmNGV5
    dd($passportRequest->json(), $passportRequest->status());
});
