@extends('adminlte::page')

@section('title', 'Ver Persona Responsable')

@section('content_header')
    <h1><i class="fas fa-hands-helping text-info mr-2"></i>Detalle de Persona Responsable</h1>
@stop

@section('content')
    <div class="card card-outline card-info">
        <div class="card-header">
            <h3 class="card-title">{{ $tutor->name }}</h3>
            <div class="card-tools">
                <span class="badge badge-success">Tutor</span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%"><i class="fas fa-user mr-1"></i> Nombre</th>
                            <td>{{ $tutor->name }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-envelope mr-1"></i> Correo</th>
                            <td>{{ $tutor->email }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-phone mr-1"></i> Teléfono</th>
                            <td>{{ $tutor->telefono ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-calendar mr-1"></i> Registrado</th>
                            <td>{{ $tutor->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-wheelchair mr-1"></i> Beneficiarios a cargo</th>
                            <td>
                                @forelse($tutor->beneficiariosACargo as $beneficiario)
                                    <a href="{{ route('beneficiarios.show', $beneficiario->id) }}" class="badge badge-info">
                                        {{ $beneficiario->nombre_completo }}
                                    </a>
                                @empty
                                    <span class="badge badge-light border">Sin beneficiario asignado</span>
                                @endforelse
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('tutores.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
            @if(auth()->user()->rol === 'Administrador')
            <a href="{{ route('tutores.edit', $tutor->id) }}" class="btn btn-warning">
                <i class="fas fa-edit mr-1"></i> Editar
            </a>
            @endif
        </div>
    </div>
@stop
