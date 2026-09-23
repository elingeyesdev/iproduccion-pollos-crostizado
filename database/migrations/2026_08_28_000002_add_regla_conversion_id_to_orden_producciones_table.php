<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orden_producciones', function (Blueprint $table) {
            $table->foreignId('regla_conversion_id')->nullable()->after('fecha_programada')->constrained('reglas_conversion')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orden_producciones', function (Blueprint $table) {
            $table->dropConstrainedForeignId('regla_conversion_id');
        });
    }
};
