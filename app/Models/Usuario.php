<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Model
{
    use SoftDeletes;
    protected $table = 'usuarios';

    protected $fillable = [
        'user_id',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'ci',
        'telefono',
        'correo',
        'direccion',
        'fecha_nacimiento',
        'rol',
        'estado',
         'foto',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    // Nombre completo calculado
    public function getNombreCompletoAttribute()
    {
        return trim("{$this->nombres} {$this->apellido_paterno} {$this->apellido_materno}");
    }

    // Mutadores — mayúsculas automáticas
    public function setNombresAttribute($value)
    {
        $this->attributes['nombres'] = strtoupper($value);
    }

    public function setApellidoPaternoAttribute($value)
    {
        $this->attributes['apellido_paterno'] = $value ? strtoupper($value) : null;
    }

    public function setApellidoMaternoAttribute($value)
    {
        $this->attributes['apellido_materno'] = $value ? strtoupper($value) : null;
    }

    public function setCiAttribute($value)
    {
        $this->attributes['ci'] = strtoupper($value);
    }

    public function setDireccionAttribute($value)
    {
        $this->attributes['direccion'] = $value ? strtoupper($value) : null;
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeInactivos($query)
    {
        return $query->where('estado', 'inactivo');
    }

    // Cuenta de acceso al sistema vinculada a esta ficha de personal (opcional)
    public function cuentaAcceso()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Descripción legible para el registro de auditoría
    public function etiquetaAuditoria(): string
    {
        return "al usuario {$this->nombre_completo} ({$this->rol})";
    }
}