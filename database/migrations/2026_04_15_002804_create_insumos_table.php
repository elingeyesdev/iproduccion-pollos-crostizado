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
            $table->foreignId('unidad_medida_id')->constrained('unidad_medidas')->onDelete('cascade');
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->integer('estado')->default(1);
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
