@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">
                <i class="fas fa-satellite-dish text-primary mr-2"></i>
                Panel de Control
            </h1>
            <small class="text-muted">Sistema Bastón Inteligente — Vista general</small>
        </div>
        <div class="text-right">
            <span class="badge badge-success p-2">
                <i class="fas fa-circle mr-1"></i> Sistema Activo
            </span>
            <br>
            <small class="text-muted" id="reloj"></small>
        </div>
    </div>
@stop

@section('content')

@if($emergenciasActivas > 0)
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle mr-1"></i>
        <strong>{{ $emergenciasActivas }}</strong>
        {{ $emergenciasActivas === 1 ? 'emergencia activa requiere' : 'emergencias activas requieren' }}
        atención.
        <a href="{{ route('alertas.index') }}" class="alert-link">Ver ahora</a>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

@if($beneficiariosSinResponsable > 0)
    <div class="alert alert-warning alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle mr-1"></i>
        <strong>{{ $beneficiariosSinResponsable }}</strong>
        {{ $beneficiariosSinResponsable === 1 ? 'beneficiario no tiene' : 'beneficiarios no tienen' }}
        una persona responsable asignada.
        <a href="{{ url('admin/tutores') }}" class="alert-link">Asignar ahora</a>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

{{-- Fila 1: Tarjetas de resumen --}}
<div class="row mt-2">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-gradient-info">
            <div class="inner">
                <h3>{{ $totalBeneficiarios }}</h3>
                <p>Beneficiarios Registrados</p>
            </div>
            <div class="icon"><i class="fas fa-wheelchair"></i></div>
            <a href="{{ url('admin/beneficiarios') }}" class="small-box-footer">
                Ver beneficiarios <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-gradient-success">
            <div class="inner">
                <h3>{{ $bastonesActivos }}</h3>
                <p>Bastones Activos</p>
            </div>
            <div class="icon"><i class="fas fa-walking"></i></div>
            <a href="{{ url('admin/bastones') }}" class="small-box-footer">
                Ver bastones <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-gradient-warning">
            <div class="inner">
                <h3>{{ $alertasHoy }}</h3>
                <p>Alertas Hoy</p>
            </div>
            <div class="icon"><i class="fas fa-bell"></i></div>
            <a href="{{ route('alertas.index') }}" class="small-box-footer">
                Ver alertas <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-gradient-danger">
            <div class="inner">
                <h3>{{ $emergenciasActivas }}</h3>
                <p>Emergencias Activas</p>
            </div>
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            <a href="{{ route('alertas.index') }}" class="small-box-footer">
                Ver emergencias <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-gradient-secondary">
            <div class="inner">
                <h3>{{ $totalResponsables }}</h3>
                <p>Personas Responsables</p>
            </div>
            <div class="icon"><i class="fas fa-hands-helping"></i></div>
            <a href="{{ url('admin/tutores') }}" class="small-box-footer">
                Ver responsables <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-gradient-primary">
            <div class="inner">
                <h3>{{ $totalUsuarios }}
                    <small style="font-size: 1rem;">({{ $usuariosActivos }} activos)</small>
                </h3>
                <p>Usuarios (Personal del Centro)</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
            <a href="{{ url('admin/usuarios') }}" class="small-box-footer">
                Ver usuarios <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

{{-- Fila 2: Estado de bastones + Accesos rápidos --}}
<div class="row">
    <div class="col-lg-8">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-microchip mr-1"></i> Estado de Bastones
                </h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-circle text-success mr-2"></i> Conectados</span>
                        <span class="badge badge-success badge-pill">{{ $bastonesActivos }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-circle text-danger mr-2"></i> Desconectados</span>
                        <span class="badge badge-danger badge-pill">{{ $bastonesInactivos }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-battery-quarter text-warning mr-2"></i> Batería Baja</span>
                        <span class="badge badge-warning badge-pill">{{ $bastonesBatBaja }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-tools text-info mr-2"></i> En Mantenimiento</span>
                        <span class="badge badge-info badge-pill">{{ $bastonesMant }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt mr-1"></i> Accesos Rápidos
                </h3>
            </div>
            <div class="card-body">
                @if(auth()->user()->esAdministrador())
                <a href="{{ url('admin/beneficiarios/crear') }}" class="btn btn-info btn-block mb-2">
                    <i class="fas fa-user-plus mr-2"></i> Registrar Beneficiario
                </a>
                <a href="{{ url('admin/bastones/crear') }}" class="btn btn-success btn-block mb-2">
                    <i class="fas fa-plus-circle mr-2"></i> Registrar Bastón
                </a>
                <a href="{{ url('admin/usuarios/crear') }}" class="btn btn-dark btn-block mb-2">
                    <i class="fas fa-user-cog mr-2"></i> Registrar Usuario
                </a>
                <a href="{{ url('admin/tutores/crear') }}" class="btn btn-secondary btn-block mb-2">
                    <i class="fas fa-hands-helping mr-2"></i> Registrar Persona Responsable
                </a>
                @endif
                <a href="{{ route('geolocalizacion.index') }}" class="btn btn-primary btn-block mb-2">
                    <i class="fas fa-satellite-dish mr-2"></i> Ver Monitoreo
                </a>
                <a href="{{ route('alertas.index') }}" class="btn btn-warning btn-block mb-2">
                    <i class="fas fa-bell mr-2"></i> Ver Alertas
                </a>

            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script>
    function actualizarReloj() {
        const ahora = new Date();
        document.getElementById('reloj').textContent = ahora.toLocaleString('es-BO', {
            weekday: 'long', year: 'numeric', month: 'long',
            day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    }
    actualizarReloj();
    setInterval(actualizarReloj, 1000);
</script>
@stop