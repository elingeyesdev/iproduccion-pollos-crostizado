<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('producciones')) {
            return;
        }

        Schema::table('producciones', function (Blueprint $table) {
            if (!Schema::hasColumn('producciones', 'regla_conversion_id')) {
                $table->foreignId('regla_conversion_id')->nullable()->after('orden_produccion_insumo_id')->constrained('reglas_conversion')->nullOnDelete();
            }

            if (!Schema::hasColumn('producciones', 'nivel')) {
                $table->unsignedInteger('nivel')->default(0)->after('cantidad_obtenida');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('producciones')) {
            return;
        }

        Schema::table('producciones', function (Blueprint $table) {
            if (Schema::hasColumn('producciones', 'regla_conversion_id')) {
                $table->dropConstrainedForeignId('regla_conversion_id');
            }

            if (Schema::hasColumn('producciones', 'nivel')) {
                $table->dropColumn('nivel');
            }
        });
    }
};
