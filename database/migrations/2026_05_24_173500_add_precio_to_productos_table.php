<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('productos', 'precio')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->decimal('precio', 12, 2)->default(0)->after('nombre');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('productos', 'precio')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->dropColumn('precio');
            });
        }
    }
};
