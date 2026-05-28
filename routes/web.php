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
use App\Http\Controllers\ComponenteProductoController;
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
    Route::get('componente-productos/composiciones', [ComponenteProductoController::class, 'composiciones'])->name('componente-productos.composiciones');
    Route::get('componente-productos/composiciones/{producto}/edit', [ComponenteProductoController::class, 'editComposicion'])->name('componente-productos.composiciones.edit');
    Route::put('componente-productos/composiciones/{producto}', [ComponenteProductoController::class, 'updateComposicion'])->name('componente-productos.composiciones.update');
    Route::resource('componente-productos', ComponenteProductoController::class);
    Route::resource('detalle-insumo-productos', DetalleInsumoProductoController::class);
    Route::resource('produccion-productos', ProduccionProductoController::class);
    Route::resource('detalle-ingreso-bebidas', DetalleIngresoBebidaController::class);
    Route::resource('detalle-ventas', DetalleVentaController::class);
    Route::resource('orden-producciones', OrdenProduccionController::class);
    Route::resource('producciones', ProduccionController::class);
    Route::resource('ingreso-bebidas', IngresoBebidaController::class);
    Route::resource('mermas', MermaController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('insumos', InsumoController::class);
    Route::resource('ingreso-insumos', IngresoInsumoController::class);
    Route::resource('clientes', ClienteController::class);
    Route::resource('ventas', VentaController::class);
   


});




require __DIR__.'/auth.php';
