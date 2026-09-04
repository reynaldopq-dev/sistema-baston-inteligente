<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña — Sistema Bastón Electrónico</title>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(160deg, #1a1a2e 0%, #2d205c 55%, #6366f1 130%);
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 420px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.35);
            overflow: hidden;
        }

        .card-header {
            padding: 30px 32px 24px;
            text-align: center;
        }

        .card-header .logo {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px auto;
        }

        .card-header .logo i { color: white; font-size: 22px; }

        .card-header h2 {
            font-size: 21px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 6px;
        }

        .card-header p { color: #6b7280; font-size: 13px; line-height: 1.5; }

        .card-header .badge-obligatorio {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fef3c7;
            color: #92400e;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 20px;
            margin-top: 10px;
        }

        .card-body { padding: 0 32px 32px; }

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

        .hint { color: #9ca3af; font-size: 11px; margin-top: 5px; }

        .btn-guardar {
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

        .btn-guardar:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99,102,241,0.4);
        }

        .alert-danger, .alert-success {
            margin: 0 32px 16px;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-danger { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; }

        @media (max-width: 480px) {
            .card-header { padding: 24px 20px 20px; }
            .card-body { padding: 0 20px 24px; }
            .alert-danger, .alert-success { margin: 0 20px 16px; }
            .form-control { font-size: 16px; }
        }
    </style>
</head>
<body>

<div class="card">
    <div class="card-header">
        <div class="logo"><i class="fas fa-key"></i></div>
        <h2>Cambiar Contraseña</h2>
        <p>
            @if($obligatorio)
                Por seguridad, debes definir una contraseña propia antes de continuar.
            @else
                Define una nueva contraseña para tu cuenta.
            @endif
        </p>
        @if($obligatorio)
            <span class="badge-obligatorio"><i class="fas fa-shield-alt"></i> Cambio obligatorio</span>
        @endif
    </div>

    @if($errors->any())
        <div class="alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errors->first() }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="card-body">
        <form method="POST" action="{{ route('password.cambiar.guardar') }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Contraseña Actual</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password_actual"
                           class="form-control @error('password_actual') is-invalid @enderror"
                           placeholder="••••••••" required autofocus>
                </div>
                @error('password_actual')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Nueva Contraseña</label>
                <div class="input-wrapper">
                    <i class="fas fa-key"></i>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="••••••••" required>
                </div>
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @else
                    <span class="hint">Mínimo 8 caracteres, con al menos una letra y un número.</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Confirmar Nueva Contraseña</label>
                <div class="input-wrapper">
                    <i class="fas fa-key"></i>
                    <input type="password" name="password_confirmation"
                           class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn-guardar">
                <i class="fas fa-save"></i> Guardar Contraseña
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" style="text-align:center; margin-top:16px;">
            @csrf
            <button type="submit" style="background:none; border:none; color:#9ca3af; font-size:12px; cursor:pointer; text-decoration:underline;">
                Cerrar sesión
            </button>
        </form>
    </div>
</div>

</body>
</html>
