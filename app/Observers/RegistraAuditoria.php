<?php

namespace App\Observers;

use App\Models\Auditoria;
use Illuminate\Database\Eloquent\Model;

class RegistraAuditoria
{
    public function created(Model $model): void
    {
        $this->registrar($model, 'creado', "Creó {$this->etiqueta($model)}");
    }

    public function updated(Model $model): void
    {
        // 'deleted_at' se excluye porque delete()/restore() ya generan su
        // propio evento ('eliminado'/'restaurado') — sin esto quedaría un
        // "actualizado — campos: deleted_at" duplicado al lado de cada uno.
        $campos = array_diff(array_keys($model->getChanges()), ['updated_at', 'deleted_at']);

        if (empty($campos)) {
            return;
        }

        $this->registrar(
            $model,
            'actualizado',
            "Actualizó {$this->etiqueta($model)} — campos: " . implode(', ', $campos)
        );
    }

    public function deleted(Model $model): void
    {
        // forceDelete() dispara 'deleted' Y 'forceDeleted' para el mismo
        // registro — si viene de ahí, se omite este y queda solo el de abajo
        // (forceDeleted), para no duplicar la entrada.
        if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
            return;
        }

        $this->registrar($model, 'eliminado', "Eliminó {$this->etiqueta($model)}");
    }

    public function restored(Model $model): void
    {
        $this->registrar($model, 'restaurado', "Restauró {$this->etiqueta($model)}");
    }

    public function forceDeleted(Model $model): void
    {
        $this->registrar($model, 'eliminado_permanente', "Eliminó permanentemente {$this->etiqueta($model)}");
    }

    private function etiqueta(Model $model): string
    {
        return method_exists($model, 'etiquetaAuditoria')
            ? $model->etiquetaAuditoria()
            : class_basename($model) . ' #' . $model->getKey();
    }

    private function registrar(Model $model, string $accion, string $descripcion): void
    {
        Auditoria::create([
            'user_id'     => auth()->id(),
            'accion'      => $accion,
            'modelo'      => class_basename($model),
            'modelo_id'   => $model->getKey(),
            'descripcion' => $descripcion,
            'ip'          => request()->ip(),
        ]);
    }
}
