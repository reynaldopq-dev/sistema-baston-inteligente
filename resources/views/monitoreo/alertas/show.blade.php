@extends('adminlte::page')

@section('title', 'Detalle de Alerta')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-bell text-danger mr-2"></i>Detalle de Alerta #{{ $alerta->id }}</h1>
        <a href="{{ route('alertas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Volver
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

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle mr-1"></i> {{ $errors->first() }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

@php
    $beneficiario = $alerta->baston->beneficiario ?? null;
    $colores = [
        'PENDIENTE'    => 'danger',
        'ATENDIDA'     => 'warning',
        'RESUELTA'     => 'success',
        'FALSA_ALARMA' => 'secondary',
    ];
    $color = $colores[$alerta->estado] ?? 'secondary';
    $codigoBaston = $alerta->baston->codigo ?? null;
@endphp

<div class="row">
    {{-- Columna izquierda: mapa --}}
    <div class="col-lg-7">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-map-marker-alt mr-1"></i> Ubicación del SOS</h3>
            </div>
            <div class="card-body p-0">
                @if($alerta->latitud && $alerta->longitud)
                    <div id="mapa" style="height: 400px; width: 100%;"></div>
                @else
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-map-marked-alt fa-2x mb-2 d-block"></i>
                        No se registró ubicación GPS en esta alerta
                    </div>
                @endif
            </div>
        </div>

        {{-- Panel de acciones --}}
        <div class="card card-outline card-dark">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tasks mr-1"></i> Gestión de la Alerta</h3>
            </div>
            <div class="card-body">
                @if(auth()->user()->esInvitado())
                    {{-- Modo invitado: solo lectura, sin botones de acción --}}
                    <p class="mb-1">
                        <span class="badge badge-{{ $color }}">{{ $alerta->estado }}</span>
                    </p>
                    <small class="text-muted">
                        <i class="fas fa-eye mr-1"></i> Modo invitado: solo lectura.
                    </small>
                @else
                    @if($alerta->estado === 'PENDIENTE')
                        {{-- Atender (sin observación obligatoria) --}}
                        <form action="{{ route('alertas.estado', $alerta->id) }}" method="POST" class="d-inline"
                              onsubmit="return confirmarAccion('atender')">
                            @csrf
                            <input type="hidden" name="estado" value="ATENDIDA">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-hand-paper mr-1"></i> Atender
                            </button>
                        </form>

                        {{-- Falsa alarma (con observación) --}}
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#modalFalsa">
                            <i class="fas fa-ban mr-1"></i> Falsa Alarma
                        </button>

                    @elseif($alerta->estado === 'ATENDIDA')
                        {{-- Resolver (con observación) --}}
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalResolver">
                            <i class="fas fa-check mr-1"></i> Resolver
                        </button>

                        {{-- Falsa alarma (con observación) --}}
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#modalFalsa">
                            <i class="fas fa-ban mr-1"></i> Falsa Alarma
                        </button>

                    @else
                        {{-- Ya cerrada --}}
                        <p class="mb-1">
                            <span class="badge badge-{{ $color }}">{{ $alerta->estado }}</span>
                            @if($alerta->atendidaPor)
                                por <strong>{{ $alerta->atendidaPor->name }}</strong>
                            @endif
                            @if($alerta->atendida_en)
                                el {{ $alerta->atendida_en->format('d/m/Y H:i') }}
                            @endif
                        </p>
                        @if($alerta->observaciones)
                            <div class="mt-2">
                                <strong>Observaciones:</strong>
                                <p class="text-muted mb-0">{{ $alerta->observaciones }}</p>
                            </div>
                        @endif
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Columna derecha: datos --}}
    <div class="col-lg-5">
        {{-- Datos de la alerta --}}
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle mr-1"></i> Datos de la Alerta</h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span>Estado</span>
                        <span class="badge badge-{{ $color }}">{{ $alerta->estado }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span>Fecha y Hora</span>
                        <strong>{{ $alerta->created_at->format('d/m/Y H:i') }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span>Bastón</span>
                        <strong>{{ $codigoBaston ?? '--' }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span>Batería al momento</span>
                        <strong>{{ $alerta->bateria !== null ? $alerta->bateria . '%' : '--' }}</strong>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Datos del beneficiario --}}
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user mr-1"></i> Beneficiario</h3>
            </div>
            <div class="card-body">
                @if($beneficiario)
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Nombre</span>
                            <strong>{{ $beneficiario->nombre_completo }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>CI</span>
                            <strong>{{ $beneficiario->ci }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Teléfono</span>
                            <strong>{{ $beneficiario->telefono ?? '--' }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>Diagnóstico</span>
                            <strong>{{ $beneficiario->diagnostico ?? '--' }}</strong>
                        </li>
                    </ul>
                @else
                    <p class="text-muted mb-0">Este bastón no tiene beneficiario asignado.</p>
                @endif
            </div>
        </div>

        {{-- Personas a cargo --}}
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users mr-1"></i> Personas a Cargo</h3>
            </div>
            <div class="card-body p-0">
                @if($beneficiario && $beneficiario->cuidadores->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($beneficiario->cuidadores as $cuidador)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-user-shield text-success mr-2"></i>{{ $cuidador->name }}</span>
                                @if($cuidador->telefono)
                                    <a href="tel:{{ $cuidador->telefono }}" class="badge badge-success p-2">
                                        <i class="fas fa-phone mr-1"></i> {{ $cuidador->telefono }}
                                    </a>
                                @else
                                    <span class="text-muted">Sin teléfono</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted p-3 mb-0">No hay personas a cargo asignadas.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Resolver --}}
<div class="modal fade" id="modalResolver" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('alertas.estado', $alerta->id) }}" method="POST">
            @csrf
            <input type="hidden" name="estado" value="RESUELTA">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white"><i class="fas fa-check mr-1"></i> Resolver Alerta</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <label>¿Qué pasó? (obligatorio)</label>
                    <textarea name="observaciones" class="form-control" rows="3" required
                              placeholder="Ej: Se contactó al beneficiario, todo en orden."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Resolver</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Falsa Alarma --}}
<div class="modal fade" id="modalFalsa" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('alertas.estado', $alerta->id) }}" method="POST">
            @csrf
            <input type="hidden" name="estado" value="FALSA_ALARMA">
            <div class="modal-content">
                <div class="modal-header bg-secondary">
                    <h5 class="modal-title text-white"><i class="fas fa-ban mr-1"></i> Marcar Falsa Alarma</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <label>Motivo (obligatorio)</label>
                    <textarea name="observaciones" class="form-control" rows="3" required
                              placeholder="Ej: El botón se presionó por accidente."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-secondary">Confirmar</button>
                </div>
            </div>
        </form>
    </div>
</div>

@stop

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@stop

@section('js')
@if($alerta->latitud && $alerta->longitud)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const lat = {{ $alerta->latitud }};
    const lng = {{ $alerta->longitud }};

    const mapa = L.map('mapa').setView([lat, lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19
    }).addTo(mapa);

    L.marker([lat, lng]).addTo(mapa)
        .bindPopup('<b>SOS - {{ $beneficiario->nombre_completo ?? "Beneficiario" }}</b>')
        .openPopup();
</script>
@endif

{{-- Firebase: apagar el SOS cuando se cierra la alerta --}}
@if(in_array($alerta->estado, ['ATENDIDA', 'RESUELTA', 'FALSA_ALARMA']) && $codigoBaston)
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-database-compat.js"></script>
<script>
    const firebaseConfig = {
        apiKey: "AIzaSyBUofSuc1gOgjI5X5bg20w1VGQFSElfWdc",
        authDomain: "baston-inteligente-app.firebaseapp.com",
        databaseURL: "https://baston-inteligente-app-default-rtdb.firebaseio.com",
        projectId: "baston-inteligente-app",
        storageBucket: "baston-inteligente-app.firebasestorage.app",
        messagingSenderId: "857175453836",
        appId: "1:857175453836:web:d020e88811eebdd61b8ba9"
    };
    firebase.initializeApp(firebaseConfig);
    const db = firebase.database();

    // Como la alerta ya está cerrada, apagamos el SOS en Firebase
    db.ref('bastones/{{ $codigoBaston }}/telemetria/sos').set(false)
        .then(() => console.log('SOS apagado en Firebase'))
        .catch(err => console.error('Error apagando SOS:', err));
</script>
@endif

<script>
    function confirmarAccion(accion) {
        return confirm('¿Confirma que desea ' + accion + ' esta alerta?');
    }
</script>
@stop