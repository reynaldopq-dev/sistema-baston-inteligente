<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();

            // De qué bastón vino el SOS
            $table->foreignId('baston_id')
                  ->constrained('bastones')
                  ->cascadeOnDelete();

            // Dónde pasó (para el mapa)
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();

            // Batería del bastón en el momento del SOS
            $table->integer('bateria')->nullable();

            // Estado de la alerta
            $table->enum('estado', ['PENDIENTE', 'ATENDIDA', 'RESUELTA', 'FALSA_ALARMA'])
                  ->default('PENDIENTE');

            // Quién la atendió y cuándo
            $table->foreignId('atendida_por')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamp('atendida_en')->nullable();

            // Observaciones que escriba el cuidador al resolver
            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
