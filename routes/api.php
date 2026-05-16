<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClienteApiController;
use App\Http\Controllers\Api\IngresoInsumoApiController;
use App\Http\Controllers\Api\UnidadMedidaApiController;
use App\Http\Controllers\Api\InsumoApiController;
use App\Http\Controllers\Api\VentaApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login',[AuthController::class,'login']);//el segundo login es el metodo

Route::apiResource('clientes',ClienteApiController::class);
Route::apiResource('ingreso-insumos', IngresoInsumoApiController::class);
Route::apiResource('unidad-medidas', UnidadMedidaApiController::class);
Route::apiResource('insumos', InsumoApiController::class);
Route::apiResource('ventas', VentaApiController::class);

//Route::middleware('auth:sanctum')->group(function () {
 //   Route::apiResource('clientes', ClienteApiController::class);
//});