@extends('adminlte::page')

@section('title', 'Editar Bastón')

@section('content_header')
    <h1><i class="fas fa-edit text-warning mr-2"></i>Editar Bastón</h1>
@stop

@section('content')
    <div class="card card-outline card-warning">
        <div class="card-header">
            <h3 class="card-title">Editando: {{ $baston->codigo }}</h3>
        </div>
        <form action="{{ route('bastones.update', $baston->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Código <span class="text-danger">*</span></label>
                            <input type="text" name="codigo" required maxlength="50"
                                   pattern="[A-Za-z0-9\-]+"
                                   data-msg-pattern="El código solo puede contener letras, números y guiones, sin otros caracteres especiales."
                                   class="form-control @error('codigo') is-invalid @enderror"
                                   value="{{ old('codigo', $baston->codigo) }}">
                            @error('codigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Marca <span class="text-danger">*</span></label>
                            <input type="text" name="marca" required maxlength="100"
                                   pattern="[A-Za-zÁÉÍÓÚÑÜáéíóúñü0-9\-]+( [A-Za-zÁÉÍÓÚÑÜáéíóúñü0-9\-]+)*"
                                   data-msg-pattern="La marca solo puede contener letras, números y espacios simples entre palabras, sin espacios dobles, al inicio o al final."
                                   class="form-control @error('marca') is-invalid @enderror"
                                   value="{{ old('marca', $baston->marca) }}">
                            @error('marca')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Modelo <span class="text-danger">*</span></label>
                            <input type="text" name="modelo" required maxlength="100"
                                   pattern="[A-Za-zÁÉÍÓÚÑÜáéíóúñü0-9\-]+( [A-Za-zÁÉÍÓÚÑÜáéíóúñü0-9\-]+)*"
                                   data-msg-pattern="El modelo solo puede contener letras, números y espacios simples entre palabras, sin espacios dobles, al inicio o al final."
                                   class="form-control @error('modelo') is-invalid @enderror"
                                   value="{{ old('modelo', $baston->modelo) }}">
                            @error('modelo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Número de Serie <span class="text-danger">*</span></label>
                            <input type="text" name="numero_serie" required maxlength="50"
                                   pattern="[A-Za-z0-9\-]+"
                                   data-msg-pattern="El número de serie solo puede contener letras, números y guiones, sin otros caracteres especiales."
                                   class="form-control @error('numero_serie') is-invalid @enderror"
                                   value="{{ old('numero_serie', $baston->numero_serie) }}">
                            @error('numero_serie')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Fecha de Adquisición <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_adquisicion" required
                                   min="2000-01-01" max="{{ now()->format('Y-m-d') }}"
                                   class="form-control @error('fecha_adquisicion') is-invalid @enderror"
                                   value="{{ old('fecha_adquisicion', $baston->fecha_adquisicion->format('Y-m-d')) }}">
                            @error('fecha_adquisicion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Batería (%)</label>
                            <input type="number" name="bateria" min="0" max="100"
                                   class="form-control @error('bateria') is-invalid @enderror"
                                   value="{{ old('bateria', $baston->bateria) }}">
                            @error('bateria')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Estado <span class="text-danger">*</span></label>
                            <select name="estado" required
                                    class="form-control @error('estado') is-invalid @enderror">
                                <option value="activo" {{ old('estado', $baston->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('estado', $baston->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                <option value="en_mantenimiento" {{ old('estado', $baston->estado) == 'en_mantenimiento' ? 'selected' : '' }}>En Mantenimiento</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Beneficiario Asignado</label>
                            <select name="beneficiario_id"
                                    class="form-control @error('beneficiario_id') is-invalid @enderror">
                                <option value="">— Sin asignar (En stock) —</option>
                                @foreach($beneficiarios as $beneficiario)
                                    <option value="{{ $beneficiario->id }}"
                                        {{ old('beneficiario_id', $baston->beneficiario_id) == $beneficiario->id ? 'selected' : '' }}>
                                        {{ $beneficiario->apellido_paterno }}
                                        {{ $beneficiario->apellido_materno }}
                                        {{ $beneficiario->nombres }}
                                    </option>
                                @endforeach
                            </select>
                            @error('beneficiario_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Foto del Bastón</label>
                            <div class="row">
                                <div class="col-md-2">
                                    <img src="{{ $baston->foto ? asset('images/bastones/' . $baston->foto) : asset('images/default-avatar.png') }}"
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
                <a href="{{ route('bastones.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save mr-1"></i> Actualizar Bastón
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