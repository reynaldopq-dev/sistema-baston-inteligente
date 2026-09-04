@extends('adminlte::page')

@section('title', 'Ver Bastón')

@section('content_header')
    <h1><i class="fas fa-walking text-info mr-2"></i>Detalle del Bastón</h1>
@stop

@section('content')
    <div class="card card-outline card-info">
        <div class="card-header">
            <h3 class="card-title">{{ $baston->codigo }}</h3>
            <div class="card-tools">
                @if($baston->estado === 'activo')
                    <span class="badge badge-success">Activo</span>
                @elseif($baston->estado === 'inactivo')
                    <span class="badge badge-secondary">Inactivo</span>
                @else
                    <span class="badge badge-warning">En Mantenimiento</span>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <img src="{{ $baston->foto ? asset('images/bastones/' . $baston->foto) : asset('images/default-avatar.png') }}"
                         class="img-thumbnail" style="width:150px; height:150px; object-fit:cover;">
                </div>
                <div class="col-md-9">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%"><i class="fas fa-barcode mr-1"></i> Código</th>
                            <td>{{ $baston->codigo }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-tag mr-1"></i> Marca</th>
                            <td>{{ $baston->marca }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-microchip mr-1"></i> Modelo</th>
                            <td>{{ $baston->modelo }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-hashtag mr-1"></i> Número de Serie</th>
                            <td>{{ $baston->numero_serie }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-calendar mr-1"></i> Fecha de Adquisición</th>
                            <td>{{ $baston->fecha_adquisicion->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-battery-half mr-1"></i> Batería</th>
                            <td>
                                @if($baston->bateria !== null)
                                    @php
                                        if($baston->bateria > 50){
                                            $color = 'bg-success';
                                        } elseif($baston->bateria > 20){
                                            $color = 'bg-warning';
                                        } else {
                                            $color = 'bg-danger';
                                        }
                                    @endphp
                                    <div class="progress" style="height:18px;">
                                        <div class="progress-bar {{ $color }}"
                                             style="width:{{ $baston->bateria }}%">
                                            {{ $baston->bateria }}%
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-wheelchair mr-1"></i> Beneficiario Asignado</th>
                            <td>
                                @if($baston->beneficiario)
                                    {{ $baston->beneficiario->apellido_paterno }}
                                    {{ $baston->beneficiario->apellido_materno }}
                                    {{ $baston->beneficiario->nombres }}
                                @else
                                    <span class="text-muted">— Sin asignar</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-calendar-plus mr-1"></i> Registrado</th>
                            <td>{{ $baston->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('bastones.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
            @if(!auth()->user()->esInvitado())
            <a href="{{ route('bastones.edit', $baston->id) }}" class="btn btn-warning">
                <i class="fas fa-edit mr-1"></i> Editar
            </a>
            @endif
        </div>
    </div>
@stop