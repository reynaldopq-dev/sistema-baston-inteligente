@extends('adminlte::page')

@section('title', 'Editar Usuario')

@section('content_header')
    <h1><i class="fas fa-user-edit text-warning mr-2"></i>Editar Usuario</h1>
@stop

@section('content')
    <div class="card card-outline card-warning">
        <div class="card-header">
            <h3 class="card-title">Editando: {{ $usuario->nombres }} {{ $usuario->apellido_paterno }}</h3>
        </div>
        <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Apellido Paterno
                                <small class="text-muted">(obligatorio si no tiene materno)</small>
                            </label>
                            <input type="text" name="apellido_paterno" maxlength="100"
                                   pattern="[A-Za-zÁÉÍÓÚÑÜáéíóúñü]+( [A-Za-zÁÉÍÓÚÑÜáéíóúñü]+)*"
                                   data-msg-pattern="El apellido paterno solo puede contener letras y espacios simples entre palabras, sin números, caracteres especiales, ni espacios dobles, al inicio o al final."
                                   class="form-control @error('apellido_paterno') is-invalid @enderror"
                                   value="{{ old('apellido_paterno', $usuario->apellido_paterno) }}">
                            @error('apellido_paterno')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Apellido Materno
                                <small class="text-muted">(obligatorio si no tiene paterno)</small>
                            </label>
                            <input type="text" name="apellido_materno" maxlength="100"
                                   pattern="[A-Za-zÁÉÍÓÚÑÜáéíóúñü]+( [A-Za-zÁÉÍÓÚÑÜáéíóúñü]+)*"
                                   data-msg-pattern="El apellido materno solo puede contener letras y espacios simples entre palabras, sin números, caracteres especiales, ni espacios dobles, al inicio o al final."
                                   class="form-control @error('apellido_materno') is-invalid @enderror"
                                   value="{{ old('apellido_materno', $usuario->apellido_materno) }}">
                            @error('apellido_materno')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nombres <span class="text-danger">*</span></label>
                            <input type="text" name="nombres" required minlength="2" maxlength="100"
                                   pattern="[A-Za-zÁÉÍÓÚÑÜáéíóúñü]+( [A-Za-zÁÉÍÓÚÑÜáéíóúñü]+)*"
                                   data-msg-pattern="El campo nombres solo puede contener letras y espacios simples entre palabras, sin números, caracteres especiales, ni espacios dobles, al inicio o al final."
                                   class="form-control @error('nombres') is-invalid @enderror"
                                   value="{{ old('nombres', $usuario->nombres) }}">
                            @error('nombres')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>CI <span class="text-danger">*</span></label>
                            <input type="text" name="ci" required maxlength="20"
                                   pattern="[A-Za-z0-9\-]+"
                                   data-msg-pattern="El carnet de identidad solo puede contener letras, números y guiones, sin otros caracteres especiales."
                                   class="form-control @error('ci') is-invalid @enderror"
                                   value="{{ old('ci', $usuario->ci) }}">
                            @error('ci')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" name="correo" required maxlength="255"
                                   class="form-control @error('correo') is-invalid @enderror"
                                   value="{{ old('correo', $usuario->correo) }}">
                            @error('correo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="text" name="telefono" inputmode="numeric"
                                   pattern="[0-9]{7,15}"
                                   data-msg-pattern="El teléfono debe contener solo números, entre 7 y 15 dígitos."
                                   class="form-control @error('telefono') is-invalid @enderror"
                                   value="{{ old('telefono', $usuario->telefono) }}">
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Fecha de Nacimiento <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_nacimiento" required
                                   min="1900-01-01" max="{{ now()->subYears(18)->format('Y-m-d') }}"
                                   data-msg-range="Debe ser mayor de edad (18 años) para registrarse como usuario del sistema."
                                   class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                                   value="{{ old('fecha_nacimiento', $usuario->fecha_nacimiento->format('Y-m-d')) }}">
                            @error('fecha_nacimiento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Dirección</label>
                            <input type="text" name="direccion"
                                   class="form-control @error('direccion') is-invalid @enderror"
                                   value="{{ old('direccion', $usuario->direccion) }}">
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Cuenta de acceso al sistema <span class="text-danger">*</span></label>

                            @if($usuario->user_id)
                                {{-- Ya tiene una cuenta de login vinculada: solo se puede cambiar a otra existente --}}
                                <input type="hidden" name="modo_cuenta" value="vincular">
                                <select name="user_id"
                                        class="form-control @error('user_id') is-invalid @enderror">
                                    @foreach($cuentasDisponibles as $cuenta)
                                        <option value="{{ $cuenta->id }}" {{ old('user_id', $usuario->user_id) == $cuenta->id ? 'selected' : '' }}>
                                            {{ $cuenta->name }} ({{ $cuenta->email }}) — {{ $cuenta->rol }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                <div class="custom-control custom-checkbox mt-2">
                                    <input type="checkbox" id="restablecerPassword" name="restablecer_password" value="1"
                                           class="custom-control-input" {{ old('restablecer_password') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="restablecerPassword">
                                        Restablecer contraseña de esta cuenta
                                    </label>
                                </div>
                                <small class="text-muted d-block">
                                    Genera una nueva contraseña temporal (se muestra una sola vez al guardar) y obliga a la persona a cambiarla en su próximo ingreso. Úsalo si perdió u olvidó la anterior.
                                </small>
                            @else
                                {{-- Todavía no tiene cuenta de login: puede crear una nueva o vincular una existente --}}
                                <div class="d-flex" style="gap:20px; margin-bottom:10px;">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="modoCrear" name="modo_cuenta" value="crear"
                                               class="custom-control-input"
                                               {{ old('modo_cuenta', 'crear') == 'crear' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="modoCrear">Crear nueva cuenta de acceso</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="modoVincular" name="modo_cuenta" value="vincular"
                                               class="custom-control-input"
                                               {{ old('modo_cuenta') == 'vincular' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="modoVincular">Vincular cuenta existente</label>
                                    </div>
                                </div>

                                <div id="bloqueCrear" class="alert alert-info py-2 px-3 mb-0">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Se creará un login con el correo de arriba y una <strong>contraseña temporal</strong>
                                    generada automáticamente, visible una sola vez al guardar.
                                </div>

                                <div id="bloqueVincular" style="display:none;">
                                    <select name="user_id"
                                            class="form-control @error('user_id') is-invalid @enderror">
                                        <option value="">-- Seleccione una cuenta --</option>
                                        @foreach($cuentasDisponibles as $cuenta)
                                            <option value="{{ $cuenta->id }}" {{ old('user_id') == $cuenta->id ? 'selected' : '' }}>
                                                {{ $cuenta->name }} ({{ $cuenta->email }}) — {{ $cuenta->rol }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Rol <span class="text-danger">*</span></label>
                            <select name="rol" required
                                    class="form-control @error('rol') is-invalid @enderror">
                                <option value="">-- Seleccione --</option>
                                @foreach(['Administrador', 'Médico', 'Operador', 'Cuidador'] as $rol)
                                    <option value="{{ $rol }}"
                                        {{ old('rol', $usuario->rol) == $rol ? 'selected' : '' }}>
                                        {{ $rol }}
                                    </option>
                                @endforeach
                            </select>
                            @error('rol')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Estado <span class="text-danger">*</span></label>
                            <select name="estado" required
                                    class="form-control @error('estado') is-invalid @enderror">
                                <option value="activo" {{ old('estado', $usuario->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('estado', $usuario->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Foto de Perfil</label>
                            <div class="row">
                                <div class="col-md-2">
                                    <img src="{{ $usuario->foto ? asset('images/usuarios/' . $usuario->foto) : asset('images/default-avatar.png') }}"
                                         class="img-thumbnail" style="width:100px; height:100px; object-fit:cover;">
                                </div>
                                <div class="col-md-10">
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" name="foto" id="foto"
                                                   class="custom-file-input @error('foto') is-invalid @enderror"
                                                   accept=".jpg,.jpeg,.png">
                                            <label class="custom-file-label" for="foto">
                                                Seleccionar nueva imagen (jpg, jpeg, png)
                                            </label>
                                        </div>
                                    </div>
                                    @error('foto')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Dejar vacío para mantener la foto actual. Tamaño máximo: 2MB</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save mr-1"></i> Actualizar Usuario
                </button>
            </div>
        </form>
    </div>
@stop

@section('js')
<script>
    document.getElementById('foto').addEventListener('change', function(){
        var fileName = this.files[0] ? this.files[0].name : 'Seleccionar imagen';
        this.nextElementSibling.textContent = fileName;
    });
</script>
<script>
    (function () {
        var radioCrear = document.getElementById('modoCrear');
        var radioVincular = document.getElementById('modoVincular');
        if (!radioCrear) return; // ya tiene cuenta vinculada, no hay toggle

        var bloqueCrear = document.getElementById('bloqueCrear');
        var bloqueVincular = document.getElementById('bloqueVincular');
        var selectCuenta = bloqueVincular.querySelector('select[name="user_id"]');

        function actualizar() {
            var crear = radioCrear.checked;
            bloqueCrear.style.display = crear ? 'block' : 'none';
            bloqueVincular.style.display = crear ? 'none' : 'block';
            selectCuenta.required = !crear;
        }

        radioCrear.addEventListener('change', actualizar);
        radioVincular.addEventListener('change', actualizar);
        actualizar();
    })();
</script>
<script>
    document.querySelectorAll('form').forEach(function (formulario) {
        formulario.querySelectorAll('input, select, textarea').forEach(function (campo) {
            campo.addEventListener('invalid', function () {
                var v = campo.validity;
                var mensaje;
                if (v.valueMissing) {
                    mensaje = 'Este campo es obligatorio.';
                } else if (v.patternMismatch) {
                    mensaje = campo.dataset.msgPattern || 'El formato ingresado no es válido.';
                } else if (v.typeMismatch) {
                    mensaje = 'El formato ingresado no es válido (ej: correo@dominio.com).';
                } else if (v.tooShort) {
                    mensaje = 'Debe tener al menos ' + campo.minLength + ' caracteres.';
                } else if (v.tooLong) {
                    mensaje = 'No debe exceder ' + campo.maxLength + ' caracteres.';
                } else if (v.rangeUnderflow || v.rangeOverflow) {
                    mensaje = campo.dataset.msgRange || 'El valor ingresado está fuera del rango permitido.';
                } else {
                    mensaje = 'El valor ingresado no es válido.';
                }
                campo.setCustomValidity(mensaje);
            });
            campo.addEventListener('input', function () { campo.setCustomValidity(''); });
            campo.addEventListener('change', function () { campo.setCustomValidity(''); });
        });
    });
</script>
@stop