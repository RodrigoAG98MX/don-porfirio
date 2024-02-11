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
        Schema::create('adresses', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulidMorphs('model');
            $table->string('street')->comment('calle');
            $table->string('number')->comment('número');
            $table->string('suburb')->comment('colonia');
            $table->string('cp')->comment('código postal');
            $table->string('references')->nullable()->comment('referencias');
            $table->string('state')->comment('estado');
            $table->string('country')->comment('pais');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adresses');
    }
};
