@extends('adminlte::page')

@section('title', 'Geolocalización')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-map-marker-alt text-danger mr-2"></i>Geolocalización en Tiempo Real</h1>
        <span class="badge badge-success p-2" id="estadoConexion">
            <i class="fas fa-circle mr-1"></i> Conectando...
        </span>
    </div>
@stop

@section('content')

<div class="row">
    {{-- Mapa --}}
    <div class="col-lg-9">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-map mr-1"></i> Ubicación del Bastón BST001</h3>
            </div>
            <div class="card-body p-0">
                <div id="mapa" style="height: 500px; width: 100%;"></div>
            </div>
        </div>
    </div>

    {{-- Panel de datos --}}
    <div class="col-lg-3">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle mr-1"></i> Telemetría</h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="fas fa-battery-half text-success mr-2"></i> Batería</span>
                        <span class="badge badge-success badge-pill" id="bateria">--</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="fas fa-arrow-up text-info mr-2"></i> Dist. Superior</span>
                        <span id="distSuperior">--</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="fas fa-arrow-down text-warning mr-2"></i> Dist. Inferior</span>
                        <span id="distInferior">--</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="fas fa-map-pin text-danger mr-2"></i> Latitud</span>
                        <span id="latitud">--</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="fas fa-map-pin text-danger mr-2"></i> Longitud</span>
                        <span id="longitud">--</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="fas fa-clock text-secondary mr-2"></i> Última Con.</span>
                        <span id="ultimaConexion" style="font-size:11px;">--</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Alerta SOS --}}
        <div class="card card-outline card-danger" id="cardSos" style="display:none;">
            <div class="card-body text-center bg-danger text-white">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <h4 class="mb-0">¡ALERTA SOS!</h4>
                <small>El beneficiario solicita ayuda</small>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #mapa { border-radius: 0 0 8px 8px; }
</style>
@stop

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-database-compat.js"></script>

<script>
    // ── Configuración de Firebase ──
    const firebaseConfig = {
        apiKey: "AIzaSyBUofSuc1gOgjI5X5bg20w1VGQFSElfWdc",
        authDomain: "baston-inteligente-app.firebaseapp.com",
        databaseURL: "https://baston-inteligente-app-default-rtdb.firebaseio.com",
        projectId: "baston-inteligente-app",
        storageBucket: "baston-inteligente-app.firebasestorage.app",
        messagingSenderId: "857175453836",
        appId: "1:857175453836:web:d020e88811eebdd61b8ba9",
        measurementId: "G-EE9DE6FFR0"
    };

    // Inicializar Firebase
    firebase.initializeApp(firebaseConfig);
    const db = firebase.database();

    // ── Inicializar mapa (Leaflet + OpenStreetMap) ──
    const mapa = L.map('mapa').setView([-17.3935, -66.157], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19
    }).addTo(mapa);

    // Marcador del bastón
    let marcador = L.marker([-17.3935, -66.157]).addTo(mapa)
        .bindPopup('<b>Bastón BST001</b>').openPopup();

    // Recordar el estado anterior del SOS para detectar el flanco false→true
    let sosAnterior = false;

    // ── Escuchar la telemetría en tiempo real ──
    const refTelemetria = db.ref('bastones/BST001/telemetria');

    refTelemetria.on('value', (snapshot) => {
        const datos = snapshot.val();

        if (datos) {
            // Actualizar estado de conexión
            document.getElementById('estadoConexion').innerHTML =
                '<i class="fas fa-circle mr-1"></i> Conectado';
            document.getElementById('estadoConexion').className = 'badge badge-success p-2';

            // Actualizar panel de datos
            document.getElementById('bateria').textContent = datos.bateria + '%';
            document.getElementById('distSuperior').textContent = datos.distancia_superior + ' cm';
            document.getElementById('distInferior').textContent = datos.distancia_inferior + ' cm';
            document.getElementById('latitud').textContent = datos.latitud;
            document.getElementById('longitud').textContent = datos.longitud;
            document.getElementById('ultimaConexion').textContent = datos.ultima_conexion;

            // Mover el marcador en el mapa
            if (datos.latitud && datos.longitud) {
                const nuevaPos = [datos.latitud, datos.longitud];
                marcador.setLatLng(nuevaPos);
                mapa.setView(nuevaPos, mapa.getZoom());
            }

            // Mostrar/ocultar alerta SOS
            if (datos.sos === true) {
                document.getElementById('cardSos').style.display = 'block';

                // Detectar el FLANCO: solo disparar cuando pasa de false a true
                if (!sosAnterior) {
                    registrarAlertaSos(datos);
                }
                sosAnterior = true;
            } else {
                document.getElementById('cardSos').style.display = 'none';
                sosAnterior = false;
            }
        }
    }, (error) => {
        document.getElementById('estadoConexion').innerHTML =
            '<i class="fas fa-circle mr-1"></i> Error de conexión';
        document.getElementById('estadoConexion').className = 'badge badge-danger p-2';
        console.error('Error Firebase:', error);
    });

    // ── Enviar el SOS a Laravel para que lo guarde como alerta ──
    function registrarAlertaSos(datos) {
        console.log("SOS detectado, enviando a Laravel...");
        fetch("{{ route('alertas.sos') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                codigo_baston: "BST001",
                latitud: datos.latitud,
                longitud: datos.longitud,
                bateria: datos.bateria
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.ok && !data.duplicada) {
                console.log("Alerta SOS registrada, ID:", data.alerta_id);
            } else if (data.duplicada) {
                console.log("SOS ya tenía una alerta activa, no se duplicó");
            }
        })
        .catch(error => console.error("Error al registrar SOS:", error));
    }
</script>
@stop