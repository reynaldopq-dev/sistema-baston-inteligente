<?php

namespace App\Services;

use App\Models\Alerta;
use App\Models\Baston;

/**
 * Crea una alerta de emergencia a partir de un SOS detectado, con
 * deduplicación (no abre una segunda alerta si ya hay una PENDIENTE o
 * ATENDIDA para el mismo bastón).
 *
 * Usado tanto por el endpoint que llama el JS de Geolocalización
 * (AlertaController::registrarSos, mientras alguien tiene esa pantalla
 * abierta) como por el comando programado sos:revisar (que consulta Firebase
 * directo desde el servidor, sin depender de que el navegador esté abierto).
 */
class AlertaSosService
{
    public function registrar(Baston $baston, $latitud, $longitud, $bateria): array
    {
        $alertaAbierta = Alerta::where('baston_id', $baston->id)
            ->whereIn('estado', ['PENDIENTE', 'ATENDIDA'])
            ->first();

        if ($alertaAbierta) {
            return [
                'duplicada' => true,
                'alerta'    => $alertaAbierta,
            ];
        }

        $alerta = Alerta::create([
            'baston_id' => $baston->id,
            'latitud'   => $latitud,
            'longitud'  => $longitud,
            'bateria'   => $bateria,
            'estado'    => 'PENDIENTE',
        ]);

        return [
            'duplicada' => false,
            'alerta'    => $alerta,
        ];
    }
}
