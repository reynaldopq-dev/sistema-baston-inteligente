<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    protected $table = 'alertas';

    protected $fillable = [
        'baston_id',
        'latitud',
        'longitud',
        'bateria',
        'estado',
        'atendida_por',
        'atendida_en',
        'observaciones',
    ];

    protected $casts = [
        'atendida_en' => 'datetime',
    ];

    // De qué bastón vino la alerta
    public function baston()
    {
        return $this->belongsTo(Baston::class);
    }

    // Qué usuario la atendió
    public function atendidaPor()
    {
        return $this->belongsTo(User::class, 'atendida_por');
    }

    // Atajo: llegar al beneficiario a través del bastón
    public function beneficiario()
    {
        return $this->hasOneThrough(
            Beneficiario::class,
            Baston::class,
            'id',              // llave en bastones
            'id',              // llave en beneficiarios
            'baston_id',       // llave en alertas
            'beneficiario_id'  // llave en bastones que apunta al beneficiario
        );
    }

    // Scopes útiles
    public function scopePendientes($query)
    {
        return $query->where('estado', 'PENDIENTE');
    }

    public function scopeActivas($query)
    {
        return $query->whereIn('estado', ['PENDIENTE', 'ATENDIDA']);
    }
}