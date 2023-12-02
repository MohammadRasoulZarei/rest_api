<?php

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;

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
Route::any('/test',function(){

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::apiResource('brands',BrandController::class);
Route::apiResource('categories',CategoryController::class);
Route::apiResource('products',ProductController::class);
Route::get('brands/{brand}/products',[BrandController::class,'products']);
Route::post('payment/request',[PaymentController::class,'send']);
Route::post('payment/verify',[PaymentController::class,'verify']);

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::get('/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');

