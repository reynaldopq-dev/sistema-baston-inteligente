@extends('adminlte::page')

@section('title', 'Editar Persona Responsable')

@section('content_header')
    <h1><i class="fas fa-user-edit text-warning mr-2"></i>Editar Persona Responsable</h1>
@stop

@section('content')
    <div class="card card-outline card-warning">
        <div class="card-header">
            <h3 class="card-title">Editando: {{ $tutor->name }}</h3>
        </div>
        <form action="{{ route('tutores.update', $tutor->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" required minlength="2" maxlength="150"
                                   pattern="[A-Za-zÁÉÍÓÚÑÜáéíóúñü]+( [A-Za-zÁÉÍÓÚÑÜáéíóúñü]+)*"
                                   data-msg-pattern="El nombre solo puede contener letras y espacios simples entre palabras, sin números, caracteres especiales, ni espacios dobles, al inicio o al final."
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $tutor->name) }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" name="email" required maxlength="255"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $tutor->email) }}">
                            @error('email')
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
                                   value="{{ old('telefono', $tutor->telefono) }}">
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nueva Contraseña</label>
                            <input type="password" name="password" minlength="8"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Dejar vacío para no cambiarla">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Mínimo 8 caracteres, con letras y números. Quedará como temporal: deberá cambiarla en su próximo ingreso.</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Confirmar Nueva Contraseña</label>
                            <input type="password" name="password_confirmation" minlength="8"
                                   class="form-control" placeholder="Solo si cambias la contraseña">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Beneficiarios a su cargo</label>
                            <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                @forelse($beneficiarios as $beneficiario)
                                    <div class="form-check">
                                        <input type="checkbox" name="beneficiarios[]" value="{{ $beneficiario->id }}"
                                               id="beneficiario{{ $beneficiario->id }}" class="form-check-input"
                                               {{ in_array($beneficiario->id, old('beneficiarios', $beneficiariosAsignados)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="beneficiario{{ $beneficiario->id }}">
                                            {{ $beneficiario->nombre_completo }}
                                        </label>
                                    </div>
                                @empty
                                    <span class="text-muted">No hay beneficiarios registrados todavía.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('tutores.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save mr-1"></i> Actualizar Persona Responsable
                </button>
            </div>
        </form>
    </div>
@stop

@section('js')
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
<script>
    (function () {
        var pass = document.querySelector('input[name="password"]');
        var confirmar = document.querySelector('input[name="password_confirmation"]');
        if (pass && confirmar) {
            function verificar() {
                if (confirmar.value && pass.value !== confirmar.value) {
                    confirmar.setCustomValidity('Las contraseñas no coinciden.');
                } else {
                    confirmar.setCustomValidity('');
                }
            }
            pass.addEventListener('input', verificar);
            confirmar.addEventListener('input', verificar);
        }
    })();
</script>
@stop
