<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('insumos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nombre');
            $table->foreignId('categoria_insumo_id')->constrained('categoria_insumos')->onDelete('cascade');
            $table->foreignId('unidad_medida_id')->constrained('unidad_medidas')->onDelete('cascade');
            $table->double('precioUnitario')->default(0);
            $table->integer('stockActual')->default(0);
            $table->integer('stockMinimo')->default(0);
            $table->integer('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumos');
    }
};
