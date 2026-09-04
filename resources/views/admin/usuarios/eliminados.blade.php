@extends('adminlte::page')

@section('title', 'Usuarios Eliminados')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-trash text-danger mr-2"></i>Usuarios Eliminados</h1>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Volver a Usuarios
        </a>
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

    <div class="card card-outline card-danger">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list mr-1"></i> Lista de Usuarios Eliminados
            </h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Apellidos y Nombres</th>
                        <th>CI</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Eliminado el</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
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
                            <td>{{ $usuario->deleted_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if(!auth()->user()->esInvitado())
                                <form action="{{ route('usuarios.restaurar', $usuario->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Restaurar este usuario?')">
                                    @csrf
                                    <button class="btn btn-sm btn-success" title="Restaurar">
                                        <i class="fas fa-trash-restore mr-1"></i> Restaurar
                                    </button>
                                </form>
                                <form action="{{ route('usuarios.eliminar.permanente', $usuario->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar PERMANENTEMENTE? Esta acción no se puede deshacer.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" title="Eliminar Permanente">
                                        <i class="fas fa-times-circle mr-1"></i> Eliminar
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                                No hay usuarios eliminados
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