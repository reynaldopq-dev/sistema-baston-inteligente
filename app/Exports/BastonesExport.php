<?php

namespace App\Exports;

use App\Models\Baston;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BastonesExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Baston::with('beneficiario')->orderBy('codigo')->get()->map(function($baston, $index) {
            return [
                'N°'                    => $index + 1,
                'Código'                => $baston->codigo,
                'Marca'                 => $baston->marca,
                'Modelo'                => $baston->modelo,
                'N° Serie'              => $baston->numero_serie,
                'Beneficiario Asignado' => $baston->beneficiario ? $baston->beneficiario->apellido_paterno . ' ' . $baston->beneficiario->nombres : '— Sin asignar',
                'Batería'               => $baston->bateria !== null ? $baston->bateria . '%' : '—',
                'Estado'                => ucfirst(str_replace('_', ' ', $baston->estado)),
                'Fecha Adquisición'     => $baston->fecha_adquisicion->format('d/m/Y'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'N°',
            'Código',
            'Marca',
            'Modelo',
            'N° Serie',
            'Beneficiario Asignado',
            'Batería',
            'Estado',
            'Fecha Adquisición',
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