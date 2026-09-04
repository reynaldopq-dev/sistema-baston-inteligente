<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use App\Models\User;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $auditorias = Auditoria::with('usuario')
            ->when($request->usuario_id, fn ($q, $v) => $q->where('user_id', $v))
            ->when($request->modelo, fn ($q, $v) => $q->where('modelo', $v))
            ->when($request->accion, fn ($q, $v) => $q->where('accion', $v))
            ->when($request->desde, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->hasta, fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $usuarios = User::orderBy('name')->get(['id', 'name']);

        return view('sistema.auditoria.index', compact('auditorias', 'usuarios'));
    }
}
