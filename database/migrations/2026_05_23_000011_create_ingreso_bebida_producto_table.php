<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingreso_bebida_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingreso_bebida_id')->constrained('ingreso_bebidas')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('restrict');
            $table->decimal('cantidad_ingresa', 12, 2);
            $table->integer('estado')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingreso_bebida_producto');
    }
};
