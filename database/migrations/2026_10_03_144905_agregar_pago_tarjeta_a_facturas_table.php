<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->string('tarjeta_ultimos4', 4)->nullable()->after('cambio');
            $table->string('tarjeta_aprobacion', 20)->nullable()->after('tarjeta_ultimos4');
        });
    }

    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn(['tarjeta_ultimos4', 'tarjeta_aprobacion']);
        });
    }
};
