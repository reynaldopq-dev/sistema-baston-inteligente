@extends('adminlte::page')

@section('title', 'Nuevo Beneficiario')

@section('content_header')
    <h1><i class="fas fa-user-plus text-primary mr-2"></i>Nuevo Beneficiario</h1>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-form mr-1"></i> Datos del Beneficiario</h3>
        </div>
        <form action="{{ route('beneficiarios.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Apellido Paterno</label>
                            <small class="text-muted">(obligatorio si no tiene materno)</small>
                            <input type="text" name="apellido_paterno" maxlength="100"
                                   pattern="[A-Za-zÁÉÍÓÚÑÜáéíóúñü]+( [A-Za-zÁÉÍÓÚÑÜáéíóúñü]+)*"
                                   data-msg-pattern="El apellido paterno solo puede contener letras y espacios simples entre palabras, sin números, caracteres especiales, ni espacios dobles, al inicio o al final."
                                   class="form-control @error('apellido_paterno') is-invalid @enderror"
                                   value="{{ old('apellido_paterno') }}" placeholder="Ej: PÉREZ">
                            @error('apellido_paterno')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Apellido Materno</label>
                            <small class="text-muted">(obligatorio si no tiene paterno)</small>
                            <input type="text" name="apellido_materno" maxlength="100"
                                   pattern="[A-Za-zÁÉÍÓÚÑÜáéíóúñü]+( [A-Za-zÁÉÍÓÚÑÜáéíóúñü]+)*"
                                   data-msg-pattern="El apellido materno solo puede contener letras y espacios simples entre palabras, sin números, caracteres especiales, ni espacios dobles, al inicio o al final."
                                   class="form-control @error('apellido_materno') is-invalid @enderror"
                                   value="{{ old('apellido_materno') }}" placeholder="Ej: LÓPEZ (opcional)">
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
                                   value="{{ old('nombres') }}" placeholder="Ej: JUAN CARLOS">
                            @error('nombres')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>CI (Carnet de Identidad) <span class="text-danger">*</span></label>
                            <input type="text" name="ci" required maxlength="20"
                                   pattern="[A-Za-z0-9\-]+"
                                   data-msg-pattern="El carnet de identidad solo puede contener letras, números y guiones, sin otros caracteres especiales."
                                   class="form-control @error('ci') is-invalid @enderror"
                                   value="{{ old('ci') }}" placeholder="Ej: 12345678">
                            @error('ci')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Fecha de Nacimiento <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_nacimiento" required
                                   min="1900-01-01" max="{{ now()->format('Y-m-d') }}"
                                   class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                                   value="{{ old('fecha_nacimiento') }}">
                            @error('fecha_nacimiento')
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
                                   value="{{ old('telefono') }}" placeholder="Ej: 70012345">
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Dirección</label>
                            <input type="text" name="direccion" maxlength="255"
                                   pattern="[A-Za-zÁÉÍÓÚÑÜáéíóúñü0-9#.,\-/]+( [A-Za-zÁÉÍÓÚÑÜáéíóúñü0-9#.,\-/]+)*"
                                   data-msg-pattern="La dirección contiene caracteres no permitidos, o tiene espacios dobles, al inicio o al final."
                                   class="form-control @error('direccion') is-invalid @enderror"
                                   value="{{ old('direccion') }}" placeholder="Ej: Av. Arce #123, La Paz">
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Diagnóstico <span class="text-danger">*</span></label>
                            <select name="diagnostico" required
                                    class="form-control @error('diagnostico') is-invalid @enderror">
                                <option value="">-- Seleccione --</option>
                                @foreach(['Ceguera Total','Baja Visión','Ceguera Congénita','Ceguera Adquirida','Degeneración Macular'] as $diag)
                                    <option value="{{ $diag }}" {{ old('diagnostico') == $diag ? 'selected' : '' }}>
                                        {{ $diag }}
                                    </option>
                                @endforeach
                            </select>
                            @error('diagnostico')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Estado <span class="text-danger">*</span></label>
                            <select name="estado" required
                                    class="form-control @error('estado') is-invalid @enderror">
                                <option value="activo" {{ old('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Foto de Perfil</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="foto" id="foto"
                                           class="custom-file-input @error('foto') is-invalid @enderror"
                                           accept=".jpg,.jpeg,.png">
                                    <label class="custom-file-label" for="foto">
                                        Seleccionar imagen (jpg, jpeg, png)
                                    </label>
                                </div>
                            </div>
                            @error('foto')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Tamaño máximo: 2MB</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('beneficiarios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Guardar Beneficiario
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