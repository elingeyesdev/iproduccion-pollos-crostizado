<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('ingreso_bebida_producto', 'precio')) {
            Schema::table('ingreso_bebida_producto', function (Blueprint $table) {
                $table->decimal('precio', 12, 2)->default(0)->after('cantidad_ingresa');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('ingreso_bebida_producto', 'precio')) {
            Schema::table('ingreso_bebida_producto', function (Blueprint $table) {
                $table->dropColumn('precio');
            });
        }
    }
};
