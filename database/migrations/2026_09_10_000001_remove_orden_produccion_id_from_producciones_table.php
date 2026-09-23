<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('producciones') || !Schema::hasColumn('producciones', 'orden_produccion_id')) {
            return;
        }

        Schema::table('producciones', function (Blueprint $table) {
            $table->dropForeign(['orden_produccion_id']);
            $table->dropIndex(['orden_produccion_id']);
            $table->dropColumn('orden_produccion_id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('producciones') || Schema::hasColumn('producciones', 'orden_produccion_id')) {
            return;
        }

        Schema::table('producciones', function (Blueprint $table) {
            $table->unsignedBigInteger('orden_produccion_id')->nullable()->after('id');
            $table->foreign('orden_produccion_id')->references('id')->on('orden_producciones')->nullOnDelete();
            $table->index('orden_produccion_id');
        });
    }
};