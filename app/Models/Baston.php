<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Baston extends Model
{
    use SoftDeletes;
    protected $table = 'bastones';

    protected $fillable = [
        'codigo',
        'marca',
        'modelo',
        'numero_serie',
        'fecha_adquisicion',
        'estado',
        'bateria',
        'beneficiario_id',
        'foto',
    ];

    protected $casts = [
        'fecha_adquisicion' => 'date',
    ];

    // Relación — pertenece a un beneficiario
    public function beneficiario()
    {
        return $this->belongsTo(Beneficiario::class);
    }

    // Mutadores — mayúsculas automáticas
    public function setCodigoAttribute($value)
    {
        $this->attributes['codigo'] = strtoupper($value);
    }

    public function setMarcaAttribute($value)
    {
        $this->attributes['marca'] = strtoupper($value);
    }

    public function setModeloAttribute($value)
    {
        $this->attributes['modelo'] = strtoupper($value);
    }

    public function setNumeroSerieAttribute($value)
    {
        $this->attributes['numero_serie'] = strtoupper($value);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeEnStock($query)
    {
        return $query->whereNull('beneficiario_id');
    }

    // Descripción legible para el registro de auditoría
    public function etiquetaAuditoria(): string
    {
        return "el bastón {$this->codigo}";
    }
}