@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-users text-primary mr-2"></i>Usuarios</h1>
        <div>
            <a href="{{ route('usuarios.pdf') }}" class="btn btn-danger mr-2" target="_blank">
                <i class="fas fa-file-pdf mr-1"></i> Exportar PDF
            </a>
            <a href="{{ route('usuarios.excel') }}" class="btn btn-success mr-2">
                <i class="fas fa-file-excel mr-1"></i> Exportar Excel
            </a>
            <a href="{{ route('usuarios.eliminados') }}" class="btn btn-secondary mr-2">
                <i class="fas fa-trash mr-1"></i> Ver Eliminados
            </a>
            @if(auth()->user()->rol === 'Administrador')
            <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus mr-1"></i> Nuevo Usuario
            </a>
            @endif
        </div>
    </div>
@stop

@section('content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if(session('password_temporal'))
        <div class="alert alert-warning">
            <h5><i class="fas fa-key mr-1"></i> Cuenta de acceso creada</h5>
            <p class="mb-1">Entrega estos datos a la persona. Esta contraseña <strong>no se volverá a mostrar</strong> — deberá cambiarla en su primer ingreso.</p>
            <p class="mb-0">
                <strong>Correo:</strong> {{ session('correo_temporal') }}<br>
                <strong>Contraseña temporal:</strong>
                <code style="font-size:1.1em">{{ session('password_temporal') }}</code>
            </p>
        </div>
    @endif

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list mr-1"></i> Lista de Usuarios</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Foto</th>
                        <th>Apellidos y Nombres</th>
                        <th>CI</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acceso</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <img src="{{ $usuario->foto ? asset('images/usuarios/' . $usuario->foto) : asset('images/default-avatar.png') }}"
                                     style="width:45px; height:45px; object-fit:cover; border-radius:50%;">
                            </td>
                            <td>
                                {{ $usuario->apellido_paterno }}
                                {{ $usuario->apellido_materno }}
                                {{ $usuario->nombres }}
                            </td>
                            <td>{{ $usuario->ci }}</td>
                            <td>{{ $usuario->correo }}</td>
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
                            <td>
                                @if($usuario->estado === 'activo')
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                @if($usuario->cuentaAcceso && $usuario->cuentaAcceso->activo)
                                    <i class="fas fa-check-circle text-primary" title="Tiene cuenta de acceso vinculada y puede iniciar sesión"></i>
                                @elseif($usuario->cuentaAcceso)
                                    <i class="fas fa-lock text-danger" title="Cuenta de acceso desactivada — no puede iniciar sesión"></i>
                                @else
                                    <i class="fas fa-minus text-muted" title="Sin cuenta de acceso"></i>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('usuarios.show', $usuario->id) }}"
                                   class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(auth()->user()->rol === 'Administrador')
                                <a href="{{ route('usuarios.edit', $usuario->id) }}"
                                   class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('usuarios.destroy', $usuario->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar este usuario?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                No hay usuarios registrados aún
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($usuarios->hasPages())
            <div class="card-footer">
                {{ $usuarios->links() }}
            </div>
        @endif
    </div>
@stop