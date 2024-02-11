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
        Schema::create('ventas', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->integer('number_table')->comment('Mesa del servicio');
            $table->ulid('user_id')->comment('Mesero que atendió');
            $table->double('propina', 10, 2)->comment('Propina');
            $table->date('date')->comment('Fecha de la venta');
            $table->time('time')->comment('Hora de la venta');
            $table->ulid('sucursal_id')->comment('Sucursal donde se realizó la venta');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->on('users')->references('id');
            $table->foreign('sucursal_id')->on('sucursals')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
