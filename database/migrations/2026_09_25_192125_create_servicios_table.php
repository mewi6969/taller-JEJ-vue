<?php

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
    Schema::create('servicios', function (Blueprint $table) {
        $table->id();
        $table->foreignId('motocicleta_id')->constrained('motocicletas');
        $table->foreignId('mecanico_id')->nullable()->constrained('users');
        $table->text('descripcion_problema');
        $table->string('estado')->default('pendiente');
        $table->decimal('costo_mano_obra', 10, 2)->default(0);
        $table->decimal('costo_total', 10, 2)->default(0);
        $table->date('fecha_ingreso')->nullable();
        $table->date('fecha_entrega')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
