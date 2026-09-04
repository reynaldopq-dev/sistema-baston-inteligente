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
            // Antes eliminar una Persona Responsable (Tutor) era inmediato y
            // permanente, sin papelera, a diferencia de Pacientes/Bastones/
            // Usuarios. Al agregar SoftDeletes acá, una cuenta eliminada
            // también deja de poder iniciar sesión de inmediato (Eloquent
            // excluye los registros con deleted_at de las consultas de auth).
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
