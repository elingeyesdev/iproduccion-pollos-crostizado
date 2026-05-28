<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producciones', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('orden_produccion_id')->constrained('orden_producciones')->onDelete('cascade');
            $table->foreignId('receta_id')->constrained('recetas')->onDelete('cascade');
            $table->decimal('cantidad_insumo', 12, 2); // Cantidad de insumo base usado
            $table->decimal('cantidad_obtenida', 12, 2); // Cantidad de producto final obtenido
            $table->integer('estado')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producciones');
    }
};
