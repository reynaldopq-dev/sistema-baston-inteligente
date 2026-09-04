<?php

namespace App\Exports;

use App\Models\Usuario;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsuariosExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Usuario::orderBy('apellido_paterno')->get()->map(function($usuario, $index) {
            return [
                'N°'               => $index + 1,
                'Apellido Paterno' => $usuario->apellido_paterno ?? '—',
                'Apellido Materno' => $usuario->apellido_materno ?? '—',
                'Nombres'          => $usuario->nombres,
                'CI'               => $usuario->ci,
                'Correo'           => $usuario->correo,
                'Teléfono'         => $usuario->telefono ?? '—',
                'Rol'              => $usuario->rol,
                'Estado'           => ucfirst($usuario->estado),
                'Fecha Registro'   => $usuario->created_at->format('d/m/Y'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'N°',
            'Apellido Paterno',
            'Apellido Materno',
            'Nombres',
            'CI',
            'Correo',
            'Teléfono',
            'Rol',
            'Estado',
            'Fecha Registro',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '343A40']],
            ],
        ];
    }
}
