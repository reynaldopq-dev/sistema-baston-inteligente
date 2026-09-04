<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Beneficiarios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 11px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        thead {
            background-color: #343a40;
            color: white;
        }
        thead th {
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tbody td {
            padding: 6px 8px;
            font-size: 11px;
            border-bottom: 1px solid #ddd;
        }
        .badge-activo {
            background-color: #28a745;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .badge-inactivo {
            background-color: #6c757d;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .total {
            margin-top: 10px;
            font-weight: bold;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sistema Bastón Inteligente</h1>
        <h2 style="font-size:14px; margin:5px 0;">Reporte de Beneficiarios</h2>
        <p>Fecha de generación: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Apellidos y Nombres</th>
                <th>CI</th>
                <th>Teléfono</th>
                <th>Diagnóstico</th>
                <th>Estado</th>
                <th>Fecha Registro</th>
            </tr>
        </thead>
        <tbody>
            @forelse($beneficiarios as $index => $beneficiario)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        {{ $beneficiario->apellido_paterno }}
                        {{ $beneficiario->apellido_materno }}
                        {{ $beneficiario->nombres }}
                    </td>
                    <td>{{ $beneficiario->ci }}</td>
                    <td>{{ $beneficiario->telefono ?? '—' }}</td>
                    <td>{{ $beneficiario->diagnostico }}</td>
                    <td>
                        @if($beneficiario->estado === 'activo')
                            <span class="badge-activo">Activo</span>
                        @else
                            <span class="badge-inactivo">Inactivo</span>
                        @endif
                    </td>
                    <td>{{ $beneficiario->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;">No hay beneficiarios registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total">
        Total de beneficiarios: {{ $beneficiarios->count() }}
    </div>

    <div class="footer">
        Generado por Sistema Bastón Inteligente — {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>