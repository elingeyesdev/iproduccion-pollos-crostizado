<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_regla_conversion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regla_conversion_id')->constrained('reglas_conversion')->onDelete('cascade');
            $table->foreignId('insumo_id')->constrained('insumos')->onDelete('restrict');
            $table->foreignId('unidad_medida_id')->constrained('unidad_medidas')->onDelete('restrict');
            $table->decimal('cantidad', 10, 2);
            $table->string('observacion')->nullable();
            $table->integer('estado')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_regla_conversion');
    }
};
