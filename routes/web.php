<?php

use App\Http\Controllers\CategoriaInsumoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\IngresoInsumoController;
use App\Http\Controllers\ProductoFinalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserRoleAssignmentController;
use App\Http\Controllers\UnidadMedidaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\UserRoleController;

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
    Route::resource('categoria-insumos', CategoriaInsumoController::class);
    Route::resource('producto-finals', ProductoFinalController::class);
    Route::resource('categorias', CategoriaInsumoController::class);
    Route::resource('unidad-medidas', UnidadMedidaController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('insumos', InsumoController::class);
    Route::resource('ingreso-insumos', IngresoInsumoController::class);
    Route::resource('clientes', ClienteController::class);
    Route::resource('ventas', VentaController::class);


});




require __DIR__.'/auth.php';
