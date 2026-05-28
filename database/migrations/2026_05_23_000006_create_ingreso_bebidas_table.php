<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingreso_bebidas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->date('fecha_ingreso');
            $table->integer('estado')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingreso_bebidas');
    }
};
