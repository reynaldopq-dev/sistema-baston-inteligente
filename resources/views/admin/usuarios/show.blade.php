@extends('adminlte::page')

@section('title', 'Ver Usuario')

@section('content_header')
    <h1><i class="fas fa-user text-info mr-2"></i>Detalle del Usuario</h1>
@stop

@section('content')
    <div class="card card-outline card-info">
        <div class="card-header">
            <h3 class="card-title">{{ $usuario->nombre_completo }}</h3>
            <div class="card-tools">
                @if($usuario->estado === 'activo')
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
                            <th width="40%"><i class="fas fa-user mr-1"></i> Apellido Paterno</th>
                            <td>{{ $usuario->apellido_paterno ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-user mr-1"></i> Apellido Materno</th>
                            <td>{{ $usuario->apellido_materno ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-user mr-1"></i> Nombres</th>
                            <td>{{ $usuario->nombres }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-id-card mr-1"></i> CI</th>
                            <td>{{ $usuario->ci }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-envelope mr-1"></i> Correo</th>
                            <td>{{ $usuario->correo }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-phone mr-1"></i> Teléfono</th>
                            <td>{{ $usuario->telefono ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-map-marker-alt mr-1"></i> Dirección</th>
                            <td>{{ $usuario->direccion ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-birthday-cake mr-1"></i> Fecha de Nacimiento</th>
                            <td>{{ $usuario->fecha_nacimiento->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-user-tag mr-1"></i> Rol</th>
                            <td>
                                @if($usuario->rol === 'Administrador')
                                    <span class="badge badge-danger">Administrador</span>
                                @elseif($usuario->rol === 'Médico')
                                    <span class="badge badge-info">Médico</span>
                                @elseif($usuario->rol === 'Operador')
                                    <span class="badge badge-warning">Operador</span>
                                @else
                                    <span class="badge badge-success">Cuidador</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-calendar mr-1"></i> Registrado</th>
                            <td>{{ $usuario->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-key mr-1"></i> Cuenta de acceso</th>
                            <td>
                                @if($usuario->cuentaAcceso)
                                    <span class="badge badge-primary">
                                        <i class="fas fa-check mr-1"></i>{{ $usuario->cuentaAcceso->email }} ({{ $usuario->cuentaAcceso->rol }})
                                    </span>
                                @else
                                    <span class="badge badge-light border">Sin cuenta de acceso vinculada</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
            @if(auth()->user()->rol === 'Administrador')
            <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-warning">
                <i class="fas fa-edit mr-1"></i> Editar
            </a>
            @endif
        </div>
    </div>
@stop