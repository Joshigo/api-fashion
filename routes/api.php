<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PieceController;
use App\Http\Controllers\TextureController;
use App\Http\Controllers\ColorController;

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

Route::post('/register', [AuthController::class, 'registro']);
Route::post('/login', [AuthController::class, 'login']);
