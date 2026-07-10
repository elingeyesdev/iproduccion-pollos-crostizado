<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		if (Schema::hasTable('producciones')) {
			return;
		}

		Schema::create('producciones', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('orden_produccion_id')->nullable();
			$table->foreignId('orden_produccion_insumo_id')->nullable()->unique()->constrained('orden_produccion_insumo')->nullOnDelete();
			$table->unsignedBigInteger('receta_id')->nullable();
			$table->decimal('cantidad_insumo', 12, 2)->default(0);
			$table->decimal('cantidad_obtenida', 12, 2)->default(0);
			$table->integer('estado')->default(1);
			$table->timestamps();

			$table->index('orden_produccion_id');
			$table->index('receta_id');
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('producciones');
	}
};
