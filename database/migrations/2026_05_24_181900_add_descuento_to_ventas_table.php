<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('ventas', 'descuento')) {
            Schema::table('ventas', function (Blueprint $table) {
                $table->decimal('descuento', 12, 2)->default(0)->after('total');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('ventas', 'descuento')) {
            Schema::table('ventas', function (Blueprint $table) {
                $table->dropColumn('descuento');
            });
        }
    }
};
