<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('apellido_paterno')->nullable();
            $table->string('apellido_materno')->nullable();
            $table->string('ci')->unique();
            $table->date('fecha_nacimiento');
            $table->string('telefono', 20)->nullable();
            $table->string('direccion')->nullable();
            $table->enum('diagnostico', [
                'Ceguera Total',
                'Baja Visión',
                'Ceguera Congénita',
                'Ceguera Adquirida',
                'Degeneración Macular'
            ]);
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};