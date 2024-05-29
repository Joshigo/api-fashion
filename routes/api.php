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
Route::middleware('auth:sanctum')->get('user', function (Request $request) {
    return $request->user();
});


Route::middleware(['auth:sanctum', 'role:admin,supner_admin'])->group(function () {
    Route::apiResource('categories', CategoryController::class);

    Route::apiResource('order-details', OrderDetailController::class);
    Route::apiResource('orders', OrderController::class);

    Route::group(['prefix' => 'pieces'], function () {
        Route::post('/', [PieceController::class, 'store']);
        Route::post('/{piece}', [PieceController::class, 'update']);
        Route::delete('/{piece}', [PieceController::class, 'destroy']);

        // Route::get('/{piece}', [PieceController::class, 'show']);
        // Route::get('/', [PieceController::class, 'index']);
        // Route::get('/{id}/details', [PieceController::class, 'showWithRelations']);
    });
    Route::group(['prefix' => 'textures'], function () {
        // Route::get('/', [TextureController::class, 'index']);
        Route::post('/', [TextureController::class, 'store']);
        // Route::get('/{texture}', [TextureController::class, 'show']);
        Route::post('/{texture}', [TextureController::class, 'update']);
        Route::delete('/{texture}', [TextureController::class, 'destroy']);
    });
    Route::apiResource('colors', ColorController::class);
    Route::post('/register-admin', [AuthController::class, 'registro_admin']);
});
Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
    Route::post('change-password', [AuthController::class, 'changePassword']);
});

Route::group(['prefix' => 'orders'], function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::get('/{id}', [OrderController::class, 'show']);
});

Route::group(['prefix' => 'order-details'], function () {
    Route::get('/', [OrderDetailController::class, 'index']);
    Route::post('/', [OrderDetailController::class, 'store']);
    Route::get('/{order_detail}', [OrderDetailController::class, 'show']);
});

Route::group(['prefix' => 'pieces'], function () {
    Route::get('/', [PieceController::class, 'index']);
    Route::get('/{piece}', [PieceController::class, 'show']);
    Route::get('/{id}/details', [PieceController::class, 'showWithRelations']);
});

Route::group(['prefix' => 'textures'], function () {
    Route::get('/', [TextureController::class, 'index']);
    Route::get('/{texture}', [TextureController::class, 'show']);
});

Route::group(['prefix' => 'colors'], function () {
    Route::get('/', [ColorController::class, 'index']);
    Route::get('/{color}', [ColorController::class, 'show']);
});

Route::group(['prefix' => 'categories'], function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{category}', [CategoryController::class, 'show']);
});



// Route::post('/register', [AuthController::class, 'registro']);
Route::post('/login', [AuthController::class, 'login']);
