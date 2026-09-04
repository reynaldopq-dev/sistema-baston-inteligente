<?php

namespace App\Exports;

use App\Models\Beneficiario;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BeneficiariosExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Beneficiario::orderBy('apellido_paterno')->get()->map(function($beneficiario, $index) {
            return [
                'N°'                => $index + 1,
                'Apellido Paterno'  => $beneficiario->apellido_paterno ?? '—',
                'Apellido Materno'  => $beneficiario->apellido_materno ?? '—',
                'Nombres'           => $beneficiario->nombres,
                'CI'                => $beneficiario->ci,
                'Teléfono'          => $beneficiario->telefono ?? '—',
                'Dirección'         => $beneficiario->direccion ?? '—',
                'Diagnóstico'       => $beneficiario->diagnostico,
                'Estado'            => ucfirst($beneficiario->estado),
                'Fecha Registro'    => $beneficiario->created_at->format('d/m/Y'),
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
            'Teléfono',
            'Dirección',
            'Diagnóstico',
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
