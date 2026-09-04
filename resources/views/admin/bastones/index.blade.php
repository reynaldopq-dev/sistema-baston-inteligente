@extends('adminlte::page')

@section('title', 'Bastones')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-walking text-success mr-2"></i>Bastones</h1>
        <div>
            <a href="{{ route('bastones.excel') }}" class="btn btn-success mr-2">
                <i class="fas fa-file-excel mr-1"></i> Exportar Excel
            </a>
            <a href="{{ route('bastones.pdf') }}" class="btn btn-danger mr-2" target="_blank">
                <i class="fas fa-file-pdf mr-1"></i> descargar PDF
            </a>
            <a href="{{ route('bastones.eliminados') }}" class="btn btn-danger mr-2">
                <i class="fas fa-trash mr-1"></i> Ver Eliminados
            </a>
            @if(!auth()->user()->esInvitado())
            <a href="{{ route('bastones.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle mr-1"></i> Nuevo Bastón
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

    <div class="card card-outline card-success">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list mr-1"></i> Lista de Bastones</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Foto</th>
                        <th>Código</th>
                        <th>Marca / Modelo</th>
                        <th>N° Serie</th>
                        <th>Beneficiario Asignado</th>
                        <th>Batería</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bastones as $baston)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <img src="{{ $baston->foto ? asset('images/bastones/' . $baston->foto) : asset('images/default-avatar.png') }}"
                                     style="width:45px; height:45px; object-fit:cover; border-radius:50%;">
                            </td>
                            <td><strong>{{ $baston->codigo }}</strong></td>
                            <td>{{ $baston->marca }} / {{ $baston->modelo }}</td>
                            <td>{{ $baston->numero_serie }}</td>
                            <td>
                                @if($baston->beneficiario)
                                    {{ $baston->beneficiario->apellido_paterno }}
                                    {{ $baston->beneficiario->apellido_materno }}
                                    {{ $baston->beneficiario->nombres }}
                                @else
                                    <span class="text-muted">— Sin asignar</span>
                                @endif
                            </td>
                            <td>
                                @if($baston->bateria !== null)
                                    @php
                                        if($baston->bateria > 50){
                                            $color = 'bg-success';
                                        } elseif($baston->bateria > 20){
                                            $color = 'bg-warning';
                                        } else {
                                            $color = 'bg-danger';
                                        }
                                    @endphp
                                    <div class="progress" style="height:18px; min-width:80px;">
                                        <div class="progress-bar {{ $color }}"
                                             style="width:{{ $baston->bateria }}%">
                                            {{ $baston->bateria }}%
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($baston->estado === 'activo')
                                    <span class="badge badge-success">Activo</span>
                                @elseif($baston->estado === 'inactivo')
                                    <span class="badge badge-secondary">Inactivo</span>
                                @else
                                    <span class="badge badge-warning">En Mantenimiento</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('bastones.show', $baston->id) }}"
                                   class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(!auth()->user()->esInvitado())
                                <a href="{{ route('bastones.edit', $baston->id) }}"
                                   class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('bastones.destroy', $baston->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar este bastón?')">
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
                                No hay bastones registrados aún
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bastones->hasPages())
            <div class="card-footer">
                {{ $bastones->links() }}
            </div>
        @endif
    </div>
@stop