<?php

use App\Http\Controllers\CategoriaInsumoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\ProductoFinalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UnidadMedidaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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
    Route::resource('/categoria-insumo', CategoriaInsumoController::class);
    Route::resource('/producto-final', ProductoFinalController::class);
    Route::resource('/categorias', CategoriaInsumoController::class);
    Route::resource('/unidad_medida', UnidadMedidaController::class);
    Route::resource('/roles', RoleController::class);
    Route::resource('/insumo', InsumoController::class);
    Route::resource('/clientes', ClienteController::class);


});




require __DIR__.'/auth.php';
