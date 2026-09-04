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
        Schema::table('users', function (Blueprint $table) {
            // Controla si esta cuenta puede iniciar sesión. Antes, marcar el
            // "Estado" de una ficha de Usuario como Inactivo (o eliminarla)
            // no tenía ningún efecto real sobre el acceso — quedaba solo
            // cosmético en el listado de personal.
            $table->boolean('activo')->default(true)->after('debe_cambiar_password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('activo');
        });
    }
};
