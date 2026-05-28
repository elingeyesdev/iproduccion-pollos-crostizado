<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nombre');
            $table->decimal('precio', 12, 2)->default(0);
            $table->foreignId('tipo_producto_id')->constrained('tipo_productos')->onDelete('cascade');
            $table->integer('cantidad_actual')->default(0);
            $table->integer('estado')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
