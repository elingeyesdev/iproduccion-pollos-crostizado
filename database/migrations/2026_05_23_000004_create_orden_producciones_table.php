<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orden_producciones', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('numero_orden')->unique();
            $table->date('fecha_programada');
            $table->foreignId('receta_id')->nullable()->constrained('recetas')->onDelete('cascade');
            $table->decimal('cantidad_insumo', 12, 2)->default(0);
            $table->decimal('cantidad_obtenida', 12, 2)->default(0);
            $table->integer('estado')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_producciones');
    }
};
