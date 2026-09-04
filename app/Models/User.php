<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'telefono',
        'password',
        'rol',
        'debe_cambiar_password',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at'     => 'datetime',
        'password'              => 'hashed',
        'debe_cambiar_password' => 'boolean',
        'activo'                => 'boolean',
    ];

    // Verificar si es administrador
    public function esAdministrador()
    {
        return $this->rol === 'Administrador';
    }

    // Verificar si es invitado
    public function esInvitado()
    {
        return $this->rol === 'Invitado';
    }
    public function adminlte_image()
    {
        return asset('images/default-avatar.png');
    }

    public function adminlte_desc()
    {
        return $this->rol;
    }

    public function adminlte_profile_url()
    {
        return '';
    }

    // Ficha de personal (tabla 'usuarios') vinculada a esta cuenta de acceso (opcional)
    public function fichaPersonal()
    {
        return $this->hasOne(Usuario::class, 'user_id');
    }

    // Verificar si es tutor/persona responsable de un beneficiario
    public function esTutor()
    {
        return $this->rol === 'Tutor';
    }

    // Beneficiarios a cargo de este tutor/cuidador familiar
    public function beneficiariosACargo()
    {
        return $this->belongsToMany(Beneficiario::class, 'beneficiario_user');
    }
}
