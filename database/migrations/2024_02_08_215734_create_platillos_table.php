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
        Schema::create('platillos', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name')->comment('Nombre');
            $table->text('description')->comment('descripción');
            $table->time('preparation_time')->comment('tiempo de preparación');
            $table->double('cost', 10, 2)->comment('Costo de producción');
            $table->double('price', 10, 2)->comment('Precio de venta');
            $table->json('sucursal')->comment('Sucursales donde se ofrece');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platillos');
    }
};
