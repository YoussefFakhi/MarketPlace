<?php

use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ServiceController;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\OrderController;


Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

Route::get('/user',[AuthController::class,'user'])->middleware('auth:sanctum');
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index'])
        ->middleware('can:viewAny,App\Models\Category');

    Route::post('/categories', [CategoryController::class, 'store'])
        ->middleware('can:create,App\Models\Category');

    Route::put('/categories/{category}', [CategoryController::class, 'update'])
        ->middleware('can:update,category');

    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
        ->middleware('can:delete,category');
});


Route::middleware('auth:sanctum')->group(function(){
    Route::post('/services',[ServiceController::class,'store'])
    ->middleware('can:create,App\Models\Service');
    Route::put('services/{service}',[ServiceController::class,'update'])
    ->middleware('can:update,service');
    Route::delete('services/{service}',[ServiceController::class,'destroy'])
    ->middleware('can:delete,service');
    Route::get('/myservices',[ServiceController::class,'myServices']);
});

// Public route for browsing services
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{service}', [ServiceController::class, 'show']);



Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('orders', OrderController::class)->except(['destroy']);
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

