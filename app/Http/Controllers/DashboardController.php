<?php

namespace App\Http\Controllers;

use App\Models\Beneficiario;
use App\Models\Baston;
use App\Models\User;
use App\Models\Usuario;
use App\Models\Alerta;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBeneficiarios = Beneficiario::count();
        $bastonesActivos   = Baston::where('estado', 'activo')->count();
        $bastonesInactivos = Baston::where('estado', 'inactivo')->count();
        $bastonesMant      = Baston::where('estado', 'en_mantenimiento')->count();
        $bastonesBatBaja   = Baston::where('bateria', '<=', 20)->whereNotNull('bateria')->count();

        $totalResponsables           = User::where('rol', 'Tutor')->count();
        $beneficiariosSinResponsable = Beneficiario::doesntHave('cuidadores')->count();

        $totalUsuarios   = Usuario::count();
        $usuariosActivos = Usuario::where('estado', 'activo')->count();

        // Alertas registradas hoy (cualquier estado) y emergencias
        // todavia abiertas (PENDIENTE o ATENDIDA, ver Alerta::scopeActivas).
        $alertasHoy         = Alerta::whereDate('created_at', today())->count();
        $emergenciasActivas = Alerta::activas()->count();

        return view('dashboard.index', compact(
            'totalBeneficiarios',
            'bastonesActivos',
            'bastonesInactivos',
            'bastonesMant',
            'bastonesBatBaja',
            'totalResponsables',
            'beneficiariosSinResponsable',
            'totalUsuarios',
            'usuariosActivos',
            'alertasHoy',
            'emergenciasActivas'
        ));
    }
}
