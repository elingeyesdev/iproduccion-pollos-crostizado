<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingreso_insumos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('fecha_ingreso');
            $table->string('descripcion')->nullable();
            $table->integer('estado')->default(1);
        });

        Schema::create('ingreso_insumo_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingreso_insumo_id')->constrained('ingreso_insumos')->cascadeOnDelete();
            $table->foreignId('insumo_id')->constrained('insumos')->restrictOnDelete();
            $table->decimal('cantidad', 12, 2);
            $table->decimal('precio_unitario', 12, 2);
            $table->integer('estado')->default(1);
            $table->timestamps();

            $table->unique(['ingreso_insumo_id', 'insumo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingreso_insumo_detalle');
        Schema::dropIfExists('ingreso_insumos');
    }
};
