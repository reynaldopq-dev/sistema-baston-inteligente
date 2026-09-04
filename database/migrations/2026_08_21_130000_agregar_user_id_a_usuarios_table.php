<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Vincula la ficha de personal (tabla 'usuarios') con una cuenta de
     * acceso al sistema (tabla 'users'), de forma opcional.
     *
     * Esto no une ambos conceptos: 'users' sigue siendo la tabla de
     * autenticación (quién puede iniciar sesión) y 'usuarios' sigue siendo
     * el directorio de personal del centro. Lo único que cambia es que
     * ahora la relación entre ambos es explícita y consultable, en vez de
     * ser dos tablas completamente aisladas.
     */
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->foreignId('user_id')
                  ->nullable()
                  ->unique()
                  ->after('id')
                  ->constrained('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
