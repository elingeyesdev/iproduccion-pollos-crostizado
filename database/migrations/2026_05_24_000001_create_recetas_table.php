<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Una Receta define qué PRODUCTO FINAL se obtiene
        Schema::create('recetas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->decimal('cantidad_base', 12, 2)->default(1); // Ej: 1 pollo entero
            $table->decimal('rendimiento_esperado', 12, 2); // Ej: 8 (piezas)
            $table->integer('estado')->default(1);
            $table->timestamps();
        });

        // Insumos necesarios para la receta
        Schema::create('receta_insumo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receta_id')->constrained('recetas')->onDelete('cascade');
            $table->foreignId('insumo_id')->constrained('insumos')->onDelete('cascade');
            $table->decimal('cantidad_requerida', 12, 2); // Ej: 1 (unidad de pollo)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receta_insumo');
        Schema::dropIfExists('recetas');
    }
};
