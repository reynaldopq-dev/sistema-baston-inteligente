<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Alerta;
use App\Models\Baston;
use App\Models\Auditoria;
use App\Services\AlertaSosService;

class AlertaController extends Controller
{
    // Listado de alertas
    public function index()
    {
        $alertas = Alerta::with(['baston.beneficiario', 'atendidaPor'])
            ->orderByDesc('created_at')
            ->get();

        $pendientes = Alerta::where('estado', 'PENDIENTE')->count();

        return view('monitoreo.alertas.index', compact('alertas', 'pendientes'));
    }

    // Detalle de una alerta
    public function show($id)
    {
        $alerta = Alerta::with([
            'baston.beneficiario.cuidadores',
            'atendidaPor'
        ])->findOrFail($id);

        return view('monitoreo.alertas.show', compact('alerta'));
    }

    // Cambiar el estado de una alerta (atender / resolver / falsa alarma)
    public function cambiarEstado(Request $request, $id)
    {
        $alerta = Alerta::findOrFail($id);

        $nuevoEstado = $request->input('estado');

        // Validar que el estado sea uno de los permitidos
        $estadosValidos = ['ATENDIDA', 'RESUELTA', 'FALSA_ALARMA'];
        if (!in_array($nuevoEstado, $estadosValidos)) {
            return back()->with('error', 'Estado no válido.');
        }

        // Observación: obligatoria al resolver o marcar falsa alarma
        if (in_array($nuevoEstado, ['RESUELTA', 'FALSA_ALARMA'])) {
            $request->validate([
                'observaciones' => 'required|string|max:1000',
            ], [
                'observaciones.required' => 'Debe escribir una observación para cerrar la alerta.',
            ]);
        }

        // Guardar el cambio
        $alerta->estado = $nuevoEstado;
        $alerta->atendida_por = Auth::id();
        $alerta->atendida_en = now();

        if ($request->filled('observaciones')) {
            $alerta->observaciones = $request->observaciones;
        }

        $alerta->save();

        Auditoria::create([
            'user_id'     => Auth::id(),
            'accion'      => 'actualizado',
            'modelo'      => 'Alerta',
            'modelo_id'   => $alerta->id,
            'descripcion' => "Cambió la alerta #{$alerta->id} a estado {$nuevoEstado}",
            'ip'          => $request->ip(),
        ]);

        $mensajes = [
            'ATENDIDA'     => 'Alerta marcada como ATENDIDA.',
            'RESUELTA'     => 'Alerta RESUELTA correctamente.',
            'FALSA_ALARMA' => 'Alerta marcada como FALSA ALARMA.',
        ];

        return redirect()->route('alertas.show', $alerta->id)
            ->with('success', $mensajes[$nuevoEstado]);
    }

    // Recibe el SOS desde el JS y crea la alerta (con deduplicación)
    public function registrarSos(Request $request, AlertaSosService $sosService)
    {
        $request->validate([
            'codigo_baston' => 'required|string',
        ]);

        $baston = Baston::where('codigo', $request->codigo_baston)->first();

        if (!$baston) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Bastón no encontrado en el sistema',
            ], 404);
        }

        $resultado = $sosService->registrar($baston, $request->latitud, $request->longitud, $request->bateria);

        if ($resultado['duplicada']) {
            return response()->json([
                'ok' => true,
                'duplicada' => true,
                'mensaje' => 'Ya existe una alerta activa para este bastón',
                'alerta_id' => $resultado['alerta']->id,
            ]);
        }

        return response()->json([
            'ok' => true,
            'duplicada' => false,
            'mensaje' => 'Alerta registrada',
            'alerta_id' => $resultado['alerta']->id,
        ], 201);
    }
}
