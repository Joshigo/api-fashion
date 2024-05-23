<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderDetailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PieceController;
use App\Http\Controllers\TextureController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\OrderController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('categories', CategoryController::class);
Route::apiResource('pieces', PieceController::class);
Route::apiResource('textures', TextureController::class);
Route::apiResource('colors', ColorController::class);
Route::get('pieces/{id}/details', [PieceController::class, 'showWithRelations']);
Route::apiResource('order-details', OrderDetailController::class);
Route::apiResource('orders', OrderController::class);

Route::post('/register', [AuthController::class, 'registro']);
Route::post('/login', [AuthController::class, 'login']);
