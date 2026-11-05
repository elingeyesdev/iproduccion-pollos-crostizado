<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClienteApiController;
use App\Http\Controllers\Api\ComponenteProductoApiController;
use App\Http\Controllers\Api\IngresoBebidasController;
use App\Http\Controllers\Api\IngresoInsumoApiController;
use App\Http\Controllers\Api\InsumoApiController;
use App\Http\Controllers\Api\MermaController;
use App\Http\Controllers\Api\OrdenProduccionApiController;
use App\Http\Controllers\Api\ProduccionApiController;
use App\Http\Controllers\Api\ProductoApiController;
use App\Http\Controllers\Api\ReglaConversionApiController;
use App\Http\Controllers\Api\ReporteApiController;
use App\Http\Controllers\Api\RoleAssignmentApiController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TipoProductoApiController;
use App\Http\Controllers\Api\UnidadMedidaApiController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\VentaApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1');

Route::name('api.')->middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Seguridad
    Route::apiResource('usuarios', UsuarioController::class)
        ->except(['create', 'edit'])
        ->parameters(['usuarios' => 'user'])
        ->middleware('no.store.sensitive');
    Route::apiResource('roles', RoleController::class)->except(['create', 'edit']);
    Route::get('asignacion-roles', [RoleAssignmentApiController::class, 'index']);
    Route::post('asignacion-roles', [RoleAssignmentApiController::class, 'store']);
    Route::get('asignacion-roles/{assignment}', [RoleAssignmentApiController::class, 'show']);
    Route::put('asignacion-roles/{assignment}', [RoleAssignmentApiController::class, 'update']);
    Route::delete('asignacion-roles/{assignment}', [RoleAssignmentApiController::class, 'destroy']);

    // Parametrización
    Route::apiResource('clientes', ClienteApiController::class);
    Route::apiResource('unidad-medidas', UnidadMedidaApiController::class);
    Route::apiResource('insumos', InsumoApiController::class);
    Route::apiResource('productos', ProductoApiController::class);
    Route::apiResource('componentes-producto', ComponenteProductoApiController::class)->only(['index', 'show', 'store', 'update', 'destroy'])->parameters(['componentes-producto' => 'producto']);
    Route::apiResource('regla-conversion', ReglaConversionApiController::class);
    Route::apiResource('tipo-productos', TipoProductoApiController::class);

    // Transaccional
    Route::apiResource('ingreso-insumos', IngresoInsumoApiController::class);
    Route::apiResource('ingreso-bebidas', IngresoBebidasController::class);
    Route::apiResource('orden-producciones', OrdenProduccionApiController::class);
    Route::get('producciones/pendientes', [ProduccionApiController::class, 'pendientes']);
    Route::get('producciones/lote/{ordenProduccion}', [ProduccionApiController::class, 'loteCreate']);
    Route::post('producciones/lote/{ordenProduccion}', [ProduccionApiController::class, 'loteStore']);
    Route::get('producciones', [ProduccionApiController::class, 'index']);
    Route::get('producciones/{produccion}', [ProduccionApiController::class, 'show']);
    Route::apiResource('mermas', MermaController::class);
    Route::apiResource('ventas', VentaApiController::class);

    // Reportes
    Route::prefix('reportes')->middleware('no.store.sensitive')->group(function () {
        Route::get('insumos', [ReporteApiController::class, 'insumos']);
        Route::get('productos', [ReporteApiController::class, 'productos']);
        Route::get('stock', [ReporteApiController::class, 'stock']);
        Route::get('ventas-mensual', [ReporteApiController::class, 'ventasMensual']);
        Route::get('produccion-mensual', [ReporteApiController::class, 'produccionMensual']);
        Route::get('indicadores', [ReporteApiController::class, 'indicadores']);
    });
});
