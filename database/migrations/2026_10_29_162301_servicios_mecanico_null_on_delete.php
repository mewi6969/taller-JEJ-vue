<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->dropForeign(['mecanico_id']);
            $table->foreign('mecanico_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->dropForeign(['mecanico_id']);
            $table->foreign('mecanico_id')->references('id')->on('users');
        });
    }
};
