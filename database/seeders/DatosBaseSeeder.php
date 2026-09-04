<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Beneficiario;
use App\Models\Baston;

class DatosBaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Beneficiario BEN001 (de Firebase)
        $beneficiario = Beneficiario::firstOrCreate(
            ['codigo' => 'BEN001'],
            [
                'nombres'           => 'JUAN CARLOS',
                'apellido_paterno'  => 'MAMANI',
                'apellido_materno'  => 'QUISPE',
                'ci'                => '9876543',
                'fecha_nacimiento'  => '1990-05-15',
                'telefono'          => '70011122',
                'diagnostico'       => 'CEGUERA TOTAL',
                'estado'            => 'activo',
            ]
        );

        // 2) Dos usuarios cuidadores (rol Tutor)
        $cuidador1 = User::firstOrCreate(
            ['email' => 'maria.cuidadora@sistema.com'],
            [
                'name'     => 'MARIA GANDARILLAS',
                'telefono' => '71234567',
                'password' => Hash::make('tutor123'),
                'rol'      => 'Tutor',
            ]
        );

        $cuidador2 = User::firstOrCreate(
            ['email' => 'pedro.cuidador@sistema.com'],
            [
                'name'     => 'PEDRO ROJAS',
                'telefono' => '76543210',
                'password' => Hash::make('tutor123'),
                'rol'      => 'Tutor',
            ]
        );

        // 3) Bastón BST001 (debe coincidir con Firebase), asignado al beneficiario
        Baston::firstOrCreate(
            ['codigo' => 'BST001'],
            [
                'marca'              => 'GENERICO',
                'modelo'             => 'ESP32',
                'numero_serie'       => 'BST-2026-01',
                'fecha_adquisicion'  => '2026-01-15',
                'estado'             => 'activo',
                'bateria'            => 85,
                'beneficiario_id'    => $beneficiario->id,
            ]
        );

        // 4) Vincular el beneficiario con sus dos cuidadores
        $beneficiario->cuidadores()->syncWithoutDetaching([
            $cuidador1->id,
            $cuidador2->id,
        ]);
    }
}
