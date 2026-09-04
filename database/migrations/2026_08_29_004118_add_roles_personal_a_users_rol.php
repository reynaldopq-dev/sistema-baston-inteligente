<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Iguala el enum de users.rol con los roles de personal que ya
        // existen como texto libre en usuarios.rol (ficha de personal),
        // para poder crear la cuenta de acceso con el rol correcto.
        DB::statement("ALTER TABLE users MODIFY COLUMN rol ENUM('Administrador', 'Invitado', 'Tutor', 'Médico', 'Operador', 'Cuidador') NOT NULL DEFAULT 'Invitado'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN rol ENUM('Administrador', 'Invitado', 'Tutor') NOT NULL DEFAULT 'Invitado'");
    }
};
