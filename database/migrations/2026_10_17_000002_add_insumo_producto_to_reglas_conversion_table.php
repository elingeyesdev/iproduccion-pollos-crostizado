<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // La tabla se creó fuera de las migraciones; en una instalación nueva se crea completa.
        if (!Schema::hasTable('reglas_conversion')) {
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

            return;
        }

        Schema::table('reglas_conversion', function (Blueprint $table) {
            if (!Schema::hasColumn('reglas_conversion', 'insumo_id')) {
                $table->foreignId('insumo_id')->nullable()->after('id')->constrained('insumos')->nullOnDelete();
            }

            if (!Schema::hasColumn('reglas_conversion', 'producto_id')) {
                $table->foreignId('producto_id')->nullable()->after('cantidad_entrada')->constrained('productos')->nullOnDelete();
            }
        });

        // Las reglas existentes solo guardaban los nombres: se vinculan por nombre exacto (sin mayúsculas ni espacios).
        foreach (DB::table('reglas_conversion')->get() as $regla) {
            $insumoId = $regla->insumo_id ?? DB::table('insumos')
                ->whereRaw('LOWER(TRIM(nombre)) = ?', [mb_strtolower(trim((string) $regla->nombre_insumo))])
                ->value('id');

            $productoId = $regla->producto_id ?? DB::table('productos')
                ->whereRaw('LOWER(TRIM(nombre)) = ?', [mb_strtolower(trim((string) $regla->producto_resultante))])
                ->value('id');

            DB::table('reglas_conversion')->where('id', $regla->id)->update([
                'insumo_id' => $insumoId,
                'producto_id' => $productoId,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('reglas_conversion', function (Blueprint $table) {
            if (Schema::hasColumn('reglas_conversion', 'insumo_id')) {
                $table->dropConstrainedForeignId('insumo_id');
            }

            if (Schema::hasColumn('reglas_conversion', 'producto_id')) {
                $table->dropConstrainedForeignId('producto_id');
            }
        });
    }
};
