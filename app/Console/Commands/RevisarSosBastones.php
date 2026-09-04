<?php

namespace App\Console\Commands;

use App\Models\Baston;
use App\Services\AlertaSosService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Revisa directamente en Firebase si algún bastón activo tiene el SOS
 * encendido, y registra la alerta si corresponde — sin depender de que
 * alguien tenga la pantalla de Geolocalización abierta en el navegador
 * (que es de donde venía el registro hasta ahora).
 *
 * Pensado para correr cada 1 minuto vía el scheduler de Laravel
 * (ver routes/console.php).
 */
class RevisarSosBastones extends Command
{
    protected $signature = 'sos:revisar';

    protected $description = 'Consulta Firebase por cada bastón activo y registra la alerta si detecta un SOS';

    public function handle(AlertaSosService $sosService): int
    {
        $baseUrl = rtrim(config('services.firebase.database_url'), '/');
        $bastones = Baston::where('estado', 'activo')->get();

        foreach ($bastones as $baston) {
            try {
                $respuesta = Http::timeout(10)->get("{$baseUrl}/bastones/{$baston->codigo}/telemetria.json");

                if (!$respuesta->successful()) {
                    $this->warn("No se pudo consultar Firebase para el bastón {$baston->codigo} (HTTP {$respuesta->status()}).");
                    continue;
                }

                $datos = $respuesta->json();

                if (!$datos || empty($datos['sos'])) {
                    continue;
                }

                $resultado = $sosService->registrar(
                    $baston,
                    $datos['latitud'] ?? null,
                    $datos['longitud'] ?? null,
                    $datos['bateria'] ?? null
                );

                if ($resultado['duplicada']) {
                    $this->line("SOS de {$baston->codigo} ya tenía una alerta activa (#{$resultado['alerta']->id}).");
                } else {
                    $this->info("SOS detectado en {$baston->codigo} — alerta #{$resultado['alerta']->id} registrada.");
                }
            } catch (\Throwable $e) {
                Log::error("sos:revisar — error consultando el bastón {$baston->codigo}: " . $e->getMessage());
                $this->error("Error consultando el bastón {$baston->codigo}: " . $e->getMessage());
            }
        }

        return self::SUCCESS;
    }
}
