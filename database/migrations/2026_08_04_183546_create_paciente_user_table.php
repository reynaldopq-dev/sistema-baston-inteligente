<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paciente_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('paciente_id')
                  ->constrained('pacientes')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->timestamps();

            // Un mismo cuidador no puede asignarse dos veces al mismo paciente
            $table->unique(['paciente_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paciente_user');
    }
};