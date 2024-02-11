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
        Schema::create('users', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name')->comment('Nombre');
            $table->string('email')->unique()->comment('Corrreo electrónico');
            $table->string('telephone')->comment('Número de teléfono');
            $table->string('nss')->comment('Número de seguridad social');
            $table->string('rfc')->comment('Clave de Registro Federal de Contribuyentes');
            $table->double('salary')->comment('Salario dirario');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
