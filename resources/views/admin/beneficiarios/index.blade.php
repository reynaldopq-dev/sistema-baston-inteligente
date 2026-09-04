@extends('adminlte::page')

@section('title', 'Beneficiarios')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-wheelchair text-info mr-2"></i>Beneficiarios</h1>

        <div>
            <a href="{{ route('beneficiarios.excel') }}" class="btn btn-success mr-2">
                <i class="fas fa-file-excel mr-1"></i> Exportar Excel
            </a>
            <a href="{{ route('beneficiarios.pdf') }}" class="btn btn-danger mr-2" target="_blank">
                <i class="fas fa-file-pdf mr-1"></i> Exportar PDF
            </a>
            <a href="{{ route('beneficiarios.eliminados') }}" class="btn btn-danger mr-2">
                <i class="fas fa-trash mr-1"></i> Ver Eliminados
            </a>
            @if(!auth()->user()->esInvitado())
            <a href="{{ route('beneficiarios.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus mr-1"></i> Nuevo Beneficiario
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

    <div class="card card-outline card-info">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list mr-1"></i> Lista de Beneficiarios</h3>
        </div>

        {{-- Buscador --}}
        <div class="card-body pb-0">
            <form method="GET" action="{{ route('beneficiarios.index') }}" class="form-inline">
                <div class="input-group">
                    <input type="text" name="buscar" class="form-control"
                           value="{{ request('buscar') }}"
                           placeholder="Buscar por nombre, apellido o CI">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        @if(request('buscar'))
                            <a href="{{ route('beneficiarios.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Foto</th>
                        <th>Apellidos y Nombres</th>
                        <th>CI</th>
                        <th>Teléfono</th>
                        <th>Diagnóstico</th>
                        <th>Bastón Asignado</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($beneficiarios as $beneficiario)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                <img src="{{ $beneficiario->foto ? asset('images/beneficiarios/' . $beneficiario->foto) : asset('images/default-avatar.png') }}"
                                    style="width:45px; height:45px; object-fit:cover; border-radius:50%;">
                            </td>
                            <td>
                                {{ $beneficiario->apellido_paterno }}
                                {{ $beneficiario->apellido_materno }}
                                {{ $beneficiario->nombres }}
                            </td>
                            <td>{{ $beneficiario->ci }}</td>
                            <td>{{ $beneficiario->telefono ?? '—' }}</td>
                            <td>{{ $beneficiario->diagnostico }}</td>
                            <td>
                                @if($beneficiario->bastones->count() > 0)
                                    @foreach($beneficiario->bastones as $baston)
                                        <span class="badge badge-info">{{ $baston->codigo }}</span>
                                    @endforeach
                                @else
                                    <span class="badge badge-secondary">Sin asignar</span>
                                @endif
                            </td>
                            <td>
                                @if($beneficiario->estado === 'activo')
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('beneficiarios.show', $beneficiario) }}"
                                   class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(!auth()->user()->esInvitado())
                                <a href="{{ route('beneficiarios.edit', $beneficiario) }}"
                                   class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('beneficiarios.destroy', $beneficiario) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar este beneficiario?')">
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
                                @if(request('buscar'))
                                    No se encontraron beneficiarios para "{{ request('buscar') }}"
                                @else
                                    No hay beneficiarios registrados aún
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($beneficiarios->hasPages())
            <div class="card-footer">
                {{ $beneficiarios->links()->appends(request()->only('buscar')) }}
            </div>
        @endif
    </div>
@stop
