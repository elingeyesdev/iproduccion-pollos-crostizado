<?php

use App\Models\Produccion;
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
        Schema::table('producciones', function (Blueprint $table) {
            $table->foreignId('producto_id')->nullable()->after('regla_conversion_id')->constrained('productos')->nullOnDelete();
        });

        // Backfill: resuelve el producto de cada producción existente a través de su
        // regla de conversión, igual que ya se hacía en tiempo de ejecución.
        Produccion::query()
            ->whereNull('producto_id')
            ->with('reglaConversion')
            ->get()
            ->each(function (Produccion $produccion) {
                $producto = $produccion->reglaConversion?->resolverProducto();

                if ($producto) {
                    $produccion->update(['producto_id' => $producto->id]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('producciones', function (Blueprint $table) {
            $table->dropConstrainedForeignId('producto_id');
        });
    }
};
