<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Sistema Bastón Electrónico</title>
    {{-- Font Awesome local (el CDN cdnjs.cloudflare.com lo bloquea la CSP del sitio) --}}
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: #1a1a2e;
        }

        .hero {
            display: flex;
            min-height: 100vh;
        }

        /* ── PANEL IZQUIERDO ── */
        .left-panel {
            flex: 1;
            background: url('{{ asset("images/centro.png") }}') center center / cover no-repeat;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px;
            min-height: 400px;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg, rgba(26,26,46,0.85) 0%, rgba(45,32,92,0.65) 55%, rgba(99,102,241,0.45) 100%);
        }

        .left-top {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .left-top .badge-inst {
            display: inline-block;
            background: rgba(255,255,255,0.14);
            backdrop-filter: blur(6px);
            color: white;
            font-size: 11px;
            font-weight: 600;
            padding: 6px 16px;
            border-radius: 20px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 14px;
            border: 1px solid rgba(255,255,255,0.35);
        }

        .left-top h1 {
            color: white;
            font-size: 36px;
            font-weight: 700;
            line-height: 1.3;
            text-shadow: 0 2px 8px rgba(0,0,0,0.5);
        }

        .left-top p {
            color: rgba(255,255,255,0.85);
            font-size: 14px;
            margin-top: 10px;
            line-height: 1.6;
        }

        .left-bottom {
            position: relative;
            z-index: 1;
        }

        .features {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .features li {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(5px);
            color: white;
            font-size: 13px;
            padding: 8px 14px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.25);
        }

        .features li i { color: #a5b4fc; }

        /* ── PANEL DERECHO ── */
        .right-panel {
            width: 460px;
            background: white;
            display: flex;
            flex-direction: column;
            box-shadow: -10px 0 40px rgba(0,0,0,0.3);
            overflow-y: auto;
        }

        .estatua-banner {
            width: 100%;
            height: 190px;
            background: url('{{ asset("images/estatua.jpg") }}') center 20% / cover no-repeat;
            position: relative;
            flex-shrink: 0;
        }

        .estatua-banner::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(15,52,96,0.6));
        }

        .estatua-banner .overlay-text {
            position: absolute;
            bottom: 18px;
            left: 25px;
            z-index: 1;
            color: white;
        }

        .estatua-banner .overlay-text span {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
        }

        .estatua-banner .overlay-text h3 {
            font-size: 16px;
            font-weight: 700;
        }

        /* ── FORMULARIO ── */
        .login-form-section {
            padding: 30px 40px;
            flex: 1;
        }

        .login-header {
            margin-bottom: 22px;
            text-align: center;
        }

        .login-header .logo {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px auto;
        }

        .login-header .logo i { color: white; font-size: 22px; }

        .login-header h2 {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 4px;
        }

        .login-header p { color: #6b7280; font-size: 13px; }

        .form-group { margin-bottom: 16px; }

        .form-group label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper { position: relative; }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            color: #1a1a2e;
            transition: all 0.3s;
            outline: none;
            background: #f9fafb;
        }

        .form-control:focus {
            border-color: #6366f1;
            background: white;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
        }

        .form-control.is-invalid { border-color: #ef4444; }
        .invalid-feedback { color: #ef4444; font-size: 12px; margin-top: 5px; display: block; }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #6b7280;
            cursor: pointer;
            margin-bottom: 18px;
        }

        .remember-me input { accent-color: #6366f1; }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99,102,241,0.4);
        }

        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ── CONTACTO Y MAPA ── */
        .info-section {
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
            padding: 25px 40px;
        }

        .info-section h4 {
            font-size: 11px;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #4b5563;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .contact-item i { width: 20px; text-align: center; font-size: 18px; }
        .contact-item .fa-whatsapp { color: #25D366; }
        .contact-item .fa-map-marker-alt { color: #6366f1; font-size: 16px; }

        .map-container {
            margin-top: 12px;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid #e5e7eb;
        }

        .map-container #mapaContacto {
            width: 100%;
            height: 175px;
        }

        /* Evita que el mapa "atrape" el scroll de la página al pasar el mouse */
        .map-container .leaflet-container { background: #f3f4f6; }

        .footer-text {
            text-align: center;
            padding: 12px;
            color: #9ca3af;
            font-size: 11px;
            border-top: 1px solid #e5e7eb;
            background: white;
            line-height: 1.6;
        }

        /* ── RESPONSIVO ── */
        @media (max-width: 900px) {
            .hero { flex-direction: column; }
            .left-panel { min-height: 260px; flex: none; }
            .right-panel { width: 100%; }
        }

        @media (max-width: 480px) {
            .login-form-section { padding: 20px; }
            .info-section { padding: 20px; }
            .estatua-banner { height: 140px; }
            .left-top h1 { font-size: 24px; }
            .left-top p { font-size: 13px; }
            .features { gap: 8px; }
            .features li { font-size: 12px; padding: 7px 12px; }
            /* 16px evita que iOS Safari haga zoom automático al enfocar el input */
            .form-control { font-size: 16px; }
            .btn-login { font-size: 15px; padding: 14px; }
        }
    </style>
</head>
<body>

<div class="hero">

    {{-- Panel izquierdo --}}
    <div class="left-panel">
        <div class="left-top">
            <span class="badge-inst">Centro de Rehabilitación</span>
            <h1>Manuela Gandarillas<br>Cochabamba, Bolivia</h1>
            <p>Sistema web de monitoreo y gestión del bastón electrónico para personas con discapacidad visual.</p>
        </div>
        <div class="left-bottom">
            <ul class="features">
                <li><i class="fas fa-map-marker-alt"></i> Geolocalización en tiempo real</li>
                <li><i class="fas fa-bell"></i> Alertas de emergencia</li>
                <li><i class="fas fa-shield-alt"></i> Control por roles</li>
                <li><i class="fas fa-chart-bar"></i> Reportes PDF y Excel</li>
                <li><i class="fas fa-microchip"></i> Dispositivos ESP32</li>
            </ul>
        </div>
    </div>

    {{-- Panel derecho --}}
    <div class="right-panel">

        {{-- Imagen estatua --}}
        <div class="estatua-banner">
            <div class="overlay-text">
                <span>En honor a</span>
                <h3>Manuela Gandarillas</h3>
            </div>
        </div>

        {{-- Formulario --}}
        <div class="login-form-section">
            <div class="login-header">
                <div class="logo">
                    <i class="fas fa-walking"></i>
                </div>
                <h2>Bienvenido</h2>
                <p>Ingresa tus credenciales para acceder al sistema</p>
            </div>

            @if($errors->any())
                <div class="alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               placeholder="correo@ejemplo.com"
                               required autofocus>
                    </div>
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Contraseña</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="••••••••"
                               required>
                    </div>
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <label class="remember-me">
                    <input type="checkbox" name="remember">
                    Recordarme
                </label>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
            </form>
        </div>

        {{-- Contacto + Mapa --}}
        <div class="info-section">
            <h4><i class="fas fa-address-book mr-1" style="color:#6366f1"></i> Contacto</h4>

            <div class="contact-item">
                <i class="fab fa-whatsapp"></i>
                <span>+591 74821727</span>
            </div>
            <div class="contact-item">
                <i class="fas fa-map-marker-alt"></i>
                <span>Centro de Rehabilitación Manuela Gandarillas, Cochabamba</span>
            </div>

            <h4 style="margin-top:16px"><i class="fas fa-map mr-1" style="color:#6366f1"></i> Ubicación</h4>
            <div class="map-container">
                <div id="mapaContacto"></div>
            </div>
        </div>

        <div class="footer-text">
            Sistema Bastón Electrónico v1.0 — © 2026 Proyecto Sociocomunitario Productivo<br>
            Instituto Técnico Nacional de Comercio "Federico Álvarez Plata" Nocturno
        </div>

    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Mapa estático de la ubicación del centro (reemplaza el iframe de
    // OpenStreetMap, que la política CSP del sitio bloquea en frame-src).
    const marcaCentro = [-17.3726, -66.1640];

    const mapaContacto = L.map('mapaContacto', {
        zoomControl: false,
        dragging: false,
        scrollWheelZoom: false,
        doubleClickZoom: false,
        touchZoom: false,
        boxZoom: false,
        keyboard: false,
    }).setView(marcaCentro, 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19,
    }).addTo(mapaContacto);

    L.marker(marcaCentro).addTo(mapaContacto)
        .bindPopup('Centro de Rehabilitación Manuela Gandarillas');
</script>

</body>
</html>
