<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('apellido_paterno')->nullable();
            $table->string('apellido_materno')->nullable();
            $table->string('ci')->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('correo')->unique();
            $table->string('direccion')->nullable();
            $table->date('fecha_nacimiento');
            $table->enum('rol', [
                'Administrador',
                'Médico',
                'Operador',
                'Cuidador'
            ]);
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
