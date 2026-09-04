<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bastones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('marca');
            $table->string('modelo');
            $table->string('numero_serie')->unique();
            $table->date('fecha_adquisicion');
            $table->enum('estado', ['activo', 'inactivo', 'en_mantenimiento'])->default('activo');
            $table->integer('bateria')->nullable()->comment('Porcentaje 0-100');
            $table->foreignId('paciente_id')->nullable()->constrained('pacientes')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bastones');
    }
};
