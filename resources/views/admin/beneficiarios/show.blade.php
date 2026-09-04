@extends('adminlte::page')

@section('title', 'Ver Beneficiario')

@section('content_header')
    <h1><i class="fas fa-user text-info mr-2"></i>Detalle del Beneficiario</h1>
@stop

@section('content')
    <div class="card card-outline card-info">
        <div class="card-header">
            <h3 class="card-title">{{ $beneficiario->nombre_completo }}</h3>
            <div class="card-tools">
                @if($beneficiario->estado === 'activo')
                    <span class="badge badge-success">Activo</span>
                @else
                    <span class="badge badge-secondary">Inactivo</span>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">

                        <tr>
                            <th><i class="fas fa-camera mr-1"></i> Foto</th>
                            <td>
                                <img src="{{ $beneficiario->foto ? asset('images/beneficiarios/' . $beneficiario->foto) : asset('images/default-avatar.png') }}"
                                    class="img-thumbnail" style="width:100px; height:100px; object-fit:cover;">
                            </td>
                        </tr>


                        <tr>
                            <th width="40%"><i class="fas fa-user mr-1"></i> Apellido Paterno</th>
                            <td>{{ $beneficiario->apellido_paterno }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-user mr-1"></i> Apellido Materno</th>
                            <td>{{ $beneficiario->apellido_materno ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-user mr-1"></i> Nombres</th>
                            <td>{{ $beneficiario->nombres }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-id-card mr-1"></i> CI</th>
                            <td>{{ $beneficiario->ci }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-birthday-cake mr-1"></i> Fecha de Nacimiento</th>
                            <td>{{ $beneficiario->fecha_nacimiento->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-phone mr-1"></i> Teléfono</th>
                            <td>{{ $beneficiario->telefono ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-map-marker-alt mr-1"></i> Dirección</th>
                            <td>{{ $beneficiario->direccion ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-stethoscope mr-1"></i> Diagnóstico</th>
                            <td>{{ $beneficiario->diagnostico }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-calendar mr-1"></i> Registrado</th>
                            <td>{{ $beneficiario->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-hands-helping mr-1"></i> Personas Responsables
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse($beneficiario->cuidadores as $responsable)
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-user-shield text-primary mr-1"></i>
                                                <strong>{{ $responsable->name }}</strong><br>
                                                <small class="text-muted">
                                                    <i class="fas fa-envelope mr-1"></i>{{ $responsable->email }}
                                                    @if($responsable->telefono)
                                                        &nbsp;·&nbsp;<i class="fas fa-phone mr-1"></i>{{ $responsable->telefono }}
                                                    @endif
                                                </small>
                                            </div>
                                            <a href="{{ route('tutores.show', $responsable->id) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item text-center text-muted py-4">
                                        <i class="fas fa-exclamation-triangle text-warning d-block mb-1"></i>
                                        Este beneficiario aún no tiene una persona responsable asignada.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                        @if(!auth()->user()->esInvitado())
                        <div class="card-footer">
                            <a href="{{ route('tutores.index') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-hands-helping mr-1"></i> Gestionar Personas Responsables
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('beneficiarios.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
            @if(!auth()->user()->esInvitado())
            <a href="{{ route('beneficiarios.edit', $beneficiario) }}" class="btn btn-warning">
                <i class="fas fa-edit mr-1"></i> Editar
            </a>
            @endif
        </div>
    </div>
@stop