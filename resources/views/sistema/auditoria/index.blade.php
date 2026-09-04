@extends('adminlte::page')

@section('title', 'Auditoría')

@section('content_header')
    <h1><i class="fas fa-shield-alt text-dark mr-2"></i>Auditoría</h1>
@stop

@section('content')

    <div class="card card-outline card-dark">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list mr-1"></i> Registro de Actividad</h3>
        </div>

        {{-- Filtros --}}
        <div class="card-body pb-0">
            <form method="GET" action="{{ route('auditoria.index') }}" class="form-row align-items-end">
                <div class="col-md-3 form-group">
                    <label class="small text-muted mb-1">Usuario</label>
                    <select name="usuario_id" class="form-control">
                        <option value="">Todos</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" {{ request('usuario_id') == $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 form-group">
                    <label class="small text-muted mb-1">Módulo</label>
                    <select name="modelo" class="form-control">
                        <option value="">Todos</option>
                        @foreach(['Beneficiario','Baston','Usuario','Tutor','Alerta','Auth'] as $modulo)
                            <option value="{{ $modulo }}" {{ request('modelo') === $modulo ? 'selected' : '' }}>
                                {{ $modulo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 form-group">
                    <label class="small text-muted mb-1">Acción</label>
                    <select name="accion" class="form-control">
                        <option value="">Todas</option>
                        @foreach(['creado','actualizado','eliminado','restaurado','eliminado_permanente','login','login_fallido','logout'] as $accion)
                            <option value="{{ $accion }}" {{ request('accion') === $accion ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $accion)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 form-group">
                    <label class="small text-muted mb-1">Desde</label>
                    <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
                </div>
                <div class="col-md-2 form-group">
                    <label class="small text-muted mb-1">Hasta</label>
                    <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
                </div>
                <div class="col-md-1 form-group">
                    <button type="submit" class="btn btn-dark btn-block">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </form>
            @if(request()->anyFilled(['usuario_id','modelo','accion','desde','hasta']))
                <a href="{{ route('auditoria.index') }}" class="small text-muted d-inline-block mb-2">
                    <i class="fas fa-times mr-1"></i> Quitar filtros
                </a>
            @endif
        </div>

        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Módulo</th>
                        <th>Descripción</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($auditorias as $registro)
                        <tr>
                            <td style="white-space:nowrap;">{{ $registro->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $registro->usuario->name ?? 'Sistema' }}</td>
                            <td>
                                @php
                                    $colores = [
                                        'creado'                => 'success',
                                        'actualizado'           => 'info',
                                        'eliminado'              => 'danger',
                                        'restaurado'             => 'secondary',
                                        'eliminado_permanente'  => 'dark',
                                        'login'                  => 'primary',
                                        'login_fallido'          => 'warning',
                                        'logout'                 => 'secondary',
                                    ];
                                @endphp
                                <span class="badge badge-{{ $colores[$registro->accion] ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $registro->accion)) }}
                                </span>
                            </td>
                            <td>{{ $registro->modelo ?? '—' }}</td>
                            <td>{{ $registro->descripcion }}</td>
                            <td>{{ $registro->ip ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No hay registros de auditoría con esos filtros.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $auditorias->links() }}
        </div>
    </div>

@stop
