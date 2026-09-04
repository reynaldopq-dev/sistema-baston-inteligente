@extends('adminlte::page')

@section('title', 'Alertas de Emergencia')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-bell text-danger mr-2"></i>Alertas de Emergencia</h1>
        @if($pendientes > 0)
            <span class="badge badge-danger p-2" style="font-size:14px;">
                <i class="fas fa-exclamation-triangle mr-1"></i> {{ $pendientes }} pendiente(s)
            </span>
        @endif
    </div>
@stop

@section('content')

<div class="card card-outline card-danger">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list mr-1"></i> Historial de Alertas</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="bg-dark">
                <tr>
                    <th>#</th>
                    <th>Beneficiario</th>
                    <th>Bastón</th>
                    <th>Fecha y Hora</th>
                    <th>Batería</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alertas as $alerta)
                    <tr>
                        <td>{{ $alerta->id }}</td>
                        <td>
                            @if($alerta->baston && $alerta->baston->beneficiario)
                                {{ $alerta->baston->beneficiario->nombre_completo }}
                            @else
                                <span class="text-muted">Sin asignar</span>
                            @endif
                        </td>
                        <td>{{ $alerta->baston->codigo ?? '--' }}</td>
                        <td>{{ $alerta->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($alerta->bateria !== null)
                                {{ $alerta->bateria }}%
                            @else
                                --
                            @endif
                        </td>
                        <td>
                            @php
                                $colores = [
                                    'PENDIENTE'    => 'danger',
                                    'ATENDIDA'     => 'warning',
                                    'RESUELTA'     => 'success',
                                    'FALSA_ALARMA' => 'secondary',
                                ];
                                $color = $colores[$alerta->estado] ?? 'secondary';
                            @endphp
                            <span class="badge badge-{{ $color }}">{{ $alerta->estado }}</span>
                        </td>
                        <td>
                            <a href="{{ route('alertas.show', $alerta->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            No hay alertas registradas
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@stop