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
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();

            // Quién hizo la acción. Nulo en login fallido (no se sabe quién es).
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('accion');   // creado, actualizado, eliminado, restaurado, eliminado_permanente, login, login_fallido, logout
            $table->string('modelo')->nullable();     // Paciente, Baston, Usuario, Tutor, Alerta, Auth
            $table->unsignedBigInteger('modelo_id')->nullable();
            $table->text('descripcion');
            $table->string('ip', 45)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};
