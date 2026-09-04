<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $fillable = [
        'user_id',
        'accion',
        'modelo',
        'modelo_id',
        'descripcion',
        'ip',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
