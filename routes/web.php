<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\IngresoInsumoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserRoleAssignmentController;
use App\Http\Controllers\UnidadMedidaController;
use App\Http\Controllers\TipoProductoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProductoComposicionController;
use App\Http\Controllers\DetalleInsumoProductoController;
use App\Http\Controllers\ProduccionProductoController;
use App\Http\Controllers\DetalleIngresoBebidaController;
use App\Http\Controllers\DetalleVentaController;
use App\Http\Controllers\OrdenProduccionController;
use App\Http\Controllers\ProduccionController;
use App\Http\Controllers\IngresoBebidaController;
use App\Http\Controllers\MermaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\RecetaController;
use App\Http\Controllers\ReglaConversionController;
use App\Http\Controllers\ReporteController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');


    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('/users', UserController::class);
    Route::get('/users/{user}/roles', [UserRoleController::class, 'edit'])->name('users.roles.edit');
    Route::put('/users/{user}/roles', [UserRoleController::class, 'update'])->name('users.roles.update');
    Route::get('/asignacion-roles', [UserRoleAssignmentController::class, 'index'])->name('role-assignments.index');
    Route::get('/asignacion-roles/create', [UserRoleAssignmentController::class, 'create'])->name('role-assignments.create');
    Route::post('/asignacion-roles', [UserRoleAssignmentController::class, 'store'])->name('role-assignments.store');
    Route::get('/asignacion-roles/{assignment}', [UserRoleAssignmentController::class, 'show'])->name('role-assignments.show');
    Route::resource('unidad-medidas', UnidadMedidaController::class);
    Route::resource('tipo-productos', TipoProductoController::class);
    Route::resource('productos', ProductoController::class);
    

    Route::get('composicion-productos', [ProductoComposicionController::class, 'index'])->name('productos-composicion.index');
    Route::get('composicion-productos/create', [ProductoComposicionController::class, 'create'])->name('productos-composicion.create');
    Route::post('composicion-productos', [ProductoComposicionController::class, 'store'])->name('productos-composicion.store');
    Route::get('composicion-productos/{producto}/edit', [ProductoComposicionController::class, 'edit'])->name('productos-composicion.edit');
    Route::put('composicion-productos/{producto}', [ProductoComposicionController::class, 'update'])->name('productos-composicion.update');
    Route::resource('detalle-insumo-productos', DetalleInsumoProductoController::class);
    Route::resource('produccion-productos', ProduccionProductoController::class);
    Route::resource('detalle-ingreso-bebidas', DetalleIngresoBebidaController::class);
    Route::resource('detalle-ventas', DetalleVentaController::class);
    Route::resource('orden-producciones', OrdenProduccionController::class)
        ->parameters(['orden-producciones' => 'ordenProduccion']);
    Route::get('regla-conversiones', [ReglaConversionController::class, 'index'])->name('regla-conversiones.index');
    Route::get('regla-conversiones/create', [ReglaConversionController::class, 'create'])->name('regla-conversiones.create');
    Route::post('regla-conversiones', [ReglaConversionController::class, 'store'])->name('regla-conversiones.store');
    Route::get('regla-conversiones/{reglaConversion}/edit', [ReglaConversionController::class, 'edit'])->name('regla-conversiones.edit');
    Route::put('regla-conversiones/{reglaConversion}', [ReglaConversionController::class, 'update'])->name('regla-conversiones.update');
    Route::delete('regla-conversiones/{reglaConversion}', [ReglaConversionController::class, 'destroy'])->name('regla-conversiones.destroy');
    Route::get('producciones', [ProduccionController::class, 'index'])->name('producciones.index');
    Route::get('producciones/create', [ProduccionController::class, 'create'])->name('producciones.create');
    Route::post('producciones', [ProduccionController::class, 'store'])->name('producciones.store');
    Route::get('producciones/lote/{ordenProduccion}', [ProduccionController::class, 'loteCreate'])->name('producciones.lote.create');
    Route::post('producciones/lote/{ordenProduccion}', [ProduccionController::class, 'loteStore'])->name('producciones.lote.store');
    Route::get('producciones/{produccion}', [ProduccionController::class, 'show'])->name('producciones.show');

    Route::get('reportes/insumos', [ReporteController::class, 'insumos'])->name('reportes.insumos');
    Route::get('reportes/productos', [ReporteController::class, 'productos'])->name('reportes.productos');
    Route::get('reportes/ventas-mensual', [ReporteController::class, 'ventasMensual'])->name('reportes.ventas-mensual');
    Route::get('reportes/produccion-mensual', [ReporteController::class, 'produccionMensual'])->name('reportes.produccion-mensual');
    Route::get('reportes/stock', [ReporteController::class, 'stock'])->name('reportes.stock');

    Route::resource('ingreso-bebidas', IngresoBebidaController::class);
    Route::resource('mermas', MermaController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('insumos', InsumoController::class);
    Route::resource('ingreso-insumos', IngresoInsumoController::class);
    Route::resource('clientes', ClienteController::class);
    Route::resource('ventas', VentaController::class);

   
});




require __DIR__.'/auth.php';
