<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('payment/verify', function () {

    $response = Http::post(env('API_URL_ROOT') . 'payment/verify', [
        'gate' => request()->gate,
        'token' => request()->trackId

    ]);
    dd($response->json());
})->name('payment.verify');
Route::get('/test',function () {
    $response=Http::get(env('API_URL_ROOT')."categories");
    dd($response->json()['data']['Category']);
});
