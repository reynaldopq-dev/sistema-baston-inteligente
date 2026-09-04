@extends('adminlte::page')

@section('title', 'Personas Responsables Eliminadas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-trash text-danger mr-2"></i>Personas Responsables Eliminadas</h1>
        <a href="{{ route('tutores.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Volver a Personas Responsables
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
                <i class="fas fa-list mr-1"></i> Lista de Personas Responsables Eliminadas
            </h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Eliminado el</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tutores as $tutor)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $tutor->name }}</td>
                            <td>{{ $tutor->email }}</td>
                            <td>{{ $tutor->telefono ?? '—' }}</td>
                            <td>{{ $tutor->deleted_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if(auth()->user()->rol === 'Administrador')
                                <form action="{{ route('tutores.restaurar', $tutor->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Restaurar esta persona responsable?')">
                                    @csrf
                                    <button class="btn btn-sm btn-success" title="Restaurar">
                                        <i class="fas fa-trash-restore mr-1"></i> Restaurar
                                    </button>
                                </form>
                                <form action="{{ route('tutores.eliminar.permanente', $tutor->id) }}"
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
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                                No hay personas responsables eliminadas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tutores->hasPages())
            <div class="card-footer">
                {{ $tutores->links() }}
            </div>
        @endif
    </div>
@stop
