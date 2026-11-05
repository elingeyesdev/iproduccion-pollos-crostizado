<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // En bases donde la tabla se creó a mano, no se toca: la migración 2026_10_17_000002 completa sus columnas.
        if (Schema::hasTable('reglas_conversion')) {
            return;
        }

        Schema::create('reglas_conversion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insumo_id')->nullable()->constrained('insumos')->nullOnDelete();
            $table->string('nombre_insumo')->nullable();
            $table->foreignId('unidad_entrada_id')->nullable()->constrained('unidad_medidas')->nullOnDelete();
            $table->decimal('cantidad_entrada', 12, 2)->default(1);
            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();
            $table->string('producto_resultante')->nullable();
            $table->foreignId('unidad_salida_id')->nullable()->constrained('unidad_medidas')->nullOnDelete();
            $table->decimal('rendimiento_min', 12, 2)->default(0);
            $table->decimal('rendimiento_max', 12, 2)->default(0);
            $table->string('tipo_conversion')->default('estatica');
            $table->foreignId('insumo_resultante_id')->nullable()->constrained('insumos')->nullOnDelete();
            $table->integer('estado')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reglas_conversion');
    }
};
