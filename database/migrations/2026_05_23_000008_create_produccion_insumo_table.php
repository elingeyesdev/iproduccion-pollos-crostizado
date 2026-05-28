<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produccion_insumo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produccion_id')->constrained('producciones')->onDelete('cascade');
            $table->foreignId('insumo_id')->constrained('insumos')->onDelete('restrict');
            $table->decimal('cantidad', 12, 2);
            $table->integer('estado')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produccion_insumo');
    }
};
