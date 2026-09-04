@extends('adminlte::page')

@section('title', 'Personas Responsables')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-hands-helping text-primary mr-2"></i>Personas Responsables</h1>
        <div>
            <a href="{{ route('tutores.eliminados') }}" class="btn btn-secondary mr-2">
                <i class="fas fa-trash mr-1"></i> Ver Eliminados
            </a>
            @if(auth()->user()->rol === 'Administrador')
            <a href="{{ route('tutores.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus mr-1"></i> Nueva Persona Responsable
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

    <div class="alert alert-info">
        <i class="fas fa-info-circle mr-1"></i>
        Aquí se registra a los familiares o encargados que cuidan a un beneficiario puntual y necesitan su propia cuenta para ver su monitoreo (rol <strong>Tutor</strong>). Es distinto del directorio de personal del centro (sección "Usuarios").
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list mr-1"></i> Lista de Personas Responsables</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Beneficiarios a cargo</th>
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
                            <td>
                                @forelse($tutor->beneficiariosACargo as $beneficiario)
                                    <span class="badge badge-info">{{ $beneficiario->nombre_completo }}</span>
                                @empty
                                    <span class="badge badge-light border">Sin beneficiario asignado</span>
                                @endforelse
                            </td>
                            <td>
                                <a href="{{ route('tutores.show', $tutor->id) }}"
                                   class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(auth()->user()->rol === 'Administrador')
                                <a href="{{ route('tutores.edit', $tutor->id) }}"
                                   class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('tutores.destroy', $tutor->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar esta persona responsable? Ya no podrá iniciar sesión.')">
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
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                No hay personas responsables registradas aún
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
