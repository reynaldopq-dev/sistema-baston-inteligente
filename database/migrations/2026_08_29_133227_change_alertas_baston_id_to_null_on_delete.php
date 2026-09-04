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
        // Antes, eliminar un bastón permanentemente borraba en cascada todo
        // su historial de alertas SOS. Para un sistema de emergencias eso es
        // pérdida de evidencia — ahora el historial se conserva: el bastón
        // queda desvinculado (baston_id null) en vez de borrarse la alerta.
        Schema::table('alertas', function (Blueprint $table) {
            $table->dropForeign(['baston_id']);
        });

        Schema::table('alertas', function (Blueprint $table) {
            $table->unsignedBigInteger('baston_id')->nullable()->change();
        });

        Schema::table('alertas', function (Blueprint $table) {
            $table->foreign('baston_id')->references('id')->on('bastones')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            $table->dropForeign(['baston_id']);
        });

        Schema::table('alertas', function (Blueprint $table) {
            $table->unsignedBigInteger('baston_id')->nullable(false)->change();
        });

        Schema::table('alertas', function (Blueprint $table) {
            $table->foreign('baston_id')->references('id')->on('bastones')->cascadeOnDelete();
        });
    }
};
