<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MariaDB 10.4 (la versión de este proyecto) no soporta la sintaxis
        // "RENAME COLUMN" (llegó en 10.5.2), por eso se usa CHANGE COLUMN
        // con el tipo completo — funciona en cualquier versión de MySQL/MariaDB
        // y evita depender de doctrine/dbal (no instalado en este proyecto).
        DB::statement('ALTER TABLE bastones DROP FOREIGN KEY bastones_paciente_id_foreign');
        DB::statement('ALTER TABLE bastones CHANGE paciente_id beneficiario_id BIGINT UNSIGNED NULL');

        DB::statement('ALTER TABLE paciente_user DROP FOREIGN KEY paciente_user_paciente_id_foreign');
        DB::statement('ALTER TABLE paciente_user CHANGE paciente_id beneficiario_id BIGINT UNSIGNED NOT NULL');

        Schema::rename('pacientes', 'beneficiarios');
        Schema::rename('paciente_user', 'beneficiario_user');

        DB::statement('ALTER TABLE bastones ADD CONSTRAINT bastones_beneficiario_id_foreign FOREIGN KEY (beneficiario_id) REFERENCES beneficiarios(id) ON DELETE SET NULL');
        DB::statement('ALTER TABLE beneficiario_user ADD CONSTRAINT beneficiario_user_beneficiario_id_foreign FOREIGN KEY (beneficiario_id) REFERENCES beneficiarios(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE beneficiario_user DROP INDEX paciente_user_paciente_id_user_id_unique, ADD UNIQUE beneficiario_user_beneficiario_id_user_id_unique (beneficiario_id, user_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE beneficiario_user DROP INDEX beneficiario_user_beneficiario_id_user_id_unique, ADD UNIQUE paciente_user_paciente_id_user_id_unique (beneficiario_id, user_id)');
        DB::statement('ALTER TABLE beneficiario_user DROP FOREIGN KEY beneficiario_user_beneficiario_id_foreign');
        DB::statement('ALTER TABLE bastones DROP FOREIGN KEY bastones_beneficiario_id_foreign');

        Schema::rename('beneficiarios', 'pacientes');
        Schema::rename('beneficiario_user', 'paciente_user');

        DB::statement('ALTER TABLE bastones CHANGE beneficiario_id paciente_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE bastones ADD CONSTRAINT bastones_paciente_id_foreign FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE SET NULL');

        DB::statement('ALTER TABLE paciente_user CHANGE beneficiario_id paciente_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE paciente_user ADD CONSTRAINT paciente_user_paciente_id_foreign FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE');
    }
};
