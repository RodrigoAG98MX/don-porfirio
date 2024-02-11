<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('consumos', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('venta_id')->comment('Venta a la que pertecene');
            $table->ulid('platillo_id')->comment('Platillo asociado');
            $table->integer('total')->comment('Total de platillos');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('venta_id')->on('ventas')->references('id');
            $table->foreign('platillo_id')->on('platillos')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumos');
    }
};
