<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Beneficiario extends Model
{
    use SoftDeletes;
    protected $table = 'beneficiarios';

    protected $fillable = [
        'codigo',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'ci',
        'fecha_nacimiento',
        'telefono',
        'direccion',
        'diagnostico',
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
        $this->attributes['apellido_paterno'] = strtoupper($value);
    }

    public function setApellidoMaternoAttribute($value)
    {
        $this->attributes['apellido_materno'] = $value ? strtoupper($value) : null;
    }

    public function setCiAttribute($value)
    {
        $this->attributes['ci'] = strtoupper($value);
    }

    public function setTelefonoAttribute($value)
    {
        $this->attributes['telefono'] = $value ? strtoupper($value) : null;
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

    // Un beneficiario puede tener muchos bastones
    public function bastones()
    {
        return $this->hasMany(Baston::class);
    }

    // Un beneficiario puede tener varios cuidadores (usuarios del sistema)
    public function cuidadores()
    {
        return $this->belongsToMany(User::class, 'beneficiario_user');
    }

    // Descripción legible para el registro de auditoría
    public function etiquetaAuditoria(): string
    {
        return "al beneficiario {$this->nombre_completo} (CI {$this->ci})";
    }
}
