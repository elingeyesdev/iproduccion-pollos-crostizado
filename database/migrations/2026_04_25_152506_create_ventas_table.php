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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->date('fecha_venta');
            $table->text('observaciones')->nullable();
            $table->string('opcion_pago')->default('Efectivo');
            $table->decimal('descuento', 12, 2)->default(0);
            $table->double('total')->default(0);
            $table->integer('estado')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
