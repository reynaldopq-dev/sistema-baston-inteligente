<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\BeneficiarioController;
use App\Http\Controllers\Admin\BastonController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\TutorController;
use App\Http\Controllers\Monitoreo\GeolocalizacionController;
use App\Http\Controllers\Sistema\AuditoriaController;
use App\Http\Controllers\AlertaController;
use App\Http\Controllers\Auth\PasswordController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// Solo el admin da de alta cuentas (ver UsuarioController/TutorController),
// por eso se desactiva el auto-registro público. 'confirm' también se apaga:
// usaba un patrón de middleware que ya no existe en Laravel 12 y no se usa
// en ningún flujo real de la app.
Auth::routes(['register' => false, 'confirm' => false, 'verify' => false]);

// ─── ACCESO INVITADO (QR de la feria) ─────────────────────
// Ruta pública: loguea automáticamente con la cuenta compartida
// de Invitado, sin pedir usuario ni contraseña.
Route::get('/invitado', function () {
    $invitado = User::firstOrCreate(
        ['email' => 'invitado@feria.local'],
        [
            'name'     => 'Invitado Feria',
            'password' => Hash::make(Str::random(32)),
            'rol'      => 'Invitado',
        ]
    );

    Auth::login($invitado);

    return redirect('/dashboard');
})->name('invitado.acceso');

// Cambio de contraseña obligatorio: fuera del grupo 'forzar.password' de
// abajo para no generar un loop de redirección contra sí misma.
Route::middleware(['auth'])->group(function () {
    Route::get('/cambiar-password', [PasswordController::class, 'edit'])->name('password.cambiar');
    Route::put('/cambiar-password', [PasswordController::class, 'update'])->name('password.cambiar.guardar');
});

Route::middleware(['auth', 'forzar.password'])->group(function () {

    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // ── MONITOREO ──
    Route::get('monitoreo/geolocalizacion', [GeolocalizacionController::class, 'index'])
        ->name('geolocalizacion.index');

    // ─── ALERTAS DE EMERGENCIA ───────────────────────────────
    Route::prefix('admin/alertas')->group(function () {

        // Listado de alertas
        Route::get('/', [AlertaController::class, 'index'])->name('alertas.index');

        // Detalle de una alerta
        Route::get('/ver/{id}', [AlertaController::class, 'show'])->name('alertas.show');

        // Cambiar estado (atender / resolver / falsa alarma), y registrar el
        // SOS — no para Invitado: antes cualquier logueado podía llamar
        // directo a /sos y fabricar una alerta falsa a mano.
        Route::middleware(['rol:Administrador,Tutor'])->group(function () {
            Route::post('/estado/{id}', [AlertaController::class, 'cambiarEstado'])->name('alertas.estado');

            // El JS de geolocalización llama a esta ruta cuando detecta el SOS real
            Route::post('/sos', [AlertaController::class, 'registrarSos'])->name('alertas.sos');
        });
    });
    // ─── BENEFICIARIOS ───────────────────────────────────────
    // Invitado puede ver esta sección (solo lectura); crear/editar/eliminar
    // sigue restringido a Administrador más abajo.
    Route::prefix('admin/beneficiarios')->group(function () {

        Route::get('/', [BeneficiarioController::class, 'index'])->name('beneficiarios.index');
        Route::get('/ver/{id}', [BeneficiarioController::class, 'show'])->name('beneficiarios.show');
        Route::get('/pdf', [BeneficiarioController::class, 'exportarPdf'])->name('beneficiarios.pdf');
        Route::get('/excel', [BeneficiarioController::class, 'exportarExcel'])->name('beneficiarios.excel');
        Route::get('/eliminados', [BeneficiarioController::class, 'eliminados'])->name('beneficiarios.eliminados');

        Route::middleware(['rol:Administrador'])->group(function () {
            Route::get('/crear', [BeneficiarioController::class, 'create'])->name('beneficiarios.create');
            Route::post('/guardar', [BeneficiarioController::class, 'store'])->name('beneficiarios.store');
            Route::get('/editar/{id}', [BeneficiarioController::class, 'edit'])->name('beneficiarios.edit');
            Route::put('/actualizar/{id}', [BeneficiarioController::class, 'update'])->name('beneficiarios.update');
            Route::delete('/eliminar/{id}', [BeneficiarioController::class, 'destroy'])->name('beneficiarios.destroy');
            Route::post('/restaurar/{id}', [BeneficiarioController::class, 'restaurar'])->name('beneficiarios.restaurar');
            Route::delete('/eliminar-permanente/{id}', [BeneficiarioController::class, 'eliminarPermanente'])->name('beneficiarios.eliminar.permanente');
        });
    });

    // ─── BASTONES ────────────────────────────────────────────
    // Invitado puede ver esta sección (solo lectura); crear/editar/eliminar
    // sigue restringido a Administrador más abajo.
    Route::prefix('admin/bastones')->group(function () {

        Route::get('/', [BastonController::class, 'index'])->name('bastones.index');
        Route::get('/ver/{id}', [BastonController::class, 'show'])->name('bastones.show');
        Route::get('/pdf', [BastonController::class, 'exportarPdf'])->name('bastones.pdf');
        Route::get('/excel', [BastonController::class, 'exportarExcel'])->name('bastones.excel');
        Route::get('/eliminados', [BastonController::class, 'eliminados'])->name('bastones.eliminados');

        Route::middleware(['rol:Administrador'])->group(function () {
            Route::get('/crear', [BastonController::class, 'create'])->name('bastones.create');
            Route::post('/guardar', [BastonController::class, 'store'])->name('bastones.store');
            Route::get('/editar/{id}', [BastonController::class, 'edit'])->name('bastones.edit');
            Route::put('/actualizar/{id}', [BastonController::class, 'update'])->name('bastones.update');
            Route::delete('/eliminar/{id}', [BastonController::class, 'destroy'])->name('bastones.destroy');
            Route::post('/restaurar/{id}', [BastonController::class, 'restaurar'])->name('bastones.restaurar');
            Route::delete('/eliminar-permanente/{id}', [BastonController::class, 'eliminarPermanente'])->name('bastones.eliminar.permanente');
        });
    });

    // ─── USUARIOS ────────────────────────────────────────────
    // Invitado puede ver esta sección (solo lectura); crear/editar/eliminar
    // sigue restringido a Administrador más abajo.
    Route::prefix('admin/usuarios')->group(function () {

        Route::get('/', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/ver/{id}', [UsuarioController::class, 'show'])->name('usuarios.show');
        Route::get('/eliminados', [UsuarioController::class, 'eliminados'])->name('usuarios.eliminados');
        Route::get('/pdf', [UsuarioController::class, 'exportarPdf'])->name('usuarios.pdf');
        Route::get('/excel', [UsuarioController::class, 'exportarExcel'])->name('usuarios.excel');

        Route::middleware(['rol:Administrador'])->group(function () {
            Route::get('/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
            Route::post('/guardar', [UsuarioController::class, 'store'])->name('usuarios.store');
            Route::get('/editar/{id}', [UsuarioController::class, 'edit'])->name('usuarios.edit');
            Route::put('/actualizar/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
            Route::delete('/eliminar/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
            Route::post('/restaurar/{id}', [UsuarioController::class, 'restaurar'])->name('usuarios.restaurar');
            Route::delete('/eliminar-permanente/{id}', [UsuarioController::class, 'eliminarPermanente'])->name('usuarios.eliminar.permanente');
        });
    });

    // ─── PERSONAS RESPONSABLES (Tutores/cuidadores familiares) ─
    // Familiares o encargados externos que cuidan a un beneficiario puntual y
    // tienen cuenta propia para ver su monitoreo. Distinto del directorio
    // de personal del centro (arriba, "Usuarios").
    // Invitado puede ver esta sección (solo lectura); crear/editar/eliminar
    // sigue restringido a Administrador más abajo.
    Route::prefix('admin/tutores')->group(function () {

        Route::get('/', [TutorController::class, 'index'])->name('tutores.index');
        Route::get('/ver/{id}', [TutorController::class, 'show'])->name('tutores.show');
        Route::get('/eliminados', [TutorController::class, 'eliminados'])->name('tutores.eliminados');

        Route::middleware(['rol:Administrador'])->group(function () {
            Route::get('/crear', [TutorController::class, 'create'])->name('tutores.create');
            Route::post('/guardar', [TutorController::class, 'store'])->name('tutores.store');
            Route::get('/editar/{id}', [TutorController::class, 'edit'])->name('tutores.edit');
            Route::put('/actualizar/{id}', [TutorController::class, 'update'])->name('tutores.update');
            Route::delete('/eliminar/{id}', [TutorController::class, 'destroy'])->name('tutores.destroy');
            Route::post('/restaurar/{id}', [TutorController::class, 'restaurar'])->name('tutores.restaurar');
            Route::delete('/eliminar-permanente/{id}', [TutorController::class, 'eliminarPermanente'])->name('tutores.eliminar.permanente');
        });
    });

    // ─── AUDITORÍA ───────────────────────────────────────────
    // Información sensible sobre la actividad de todo el personal:
    // solo Administrador, a diferencia de los módulos de arriba que
    // sí dejan leer a Invitado.
    Route::middleware(['rol:Administrador'])->group(function () {
        Route::get('sistema/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');
    });
});