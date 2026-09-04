<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\UsuariosExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    // Evita que el sistema se quede sin ningún Administrador activo: es el
    // único rol que puede gestionar todo (crear/editar/eliminar), así que si
    // se pierde el último por error (borrado, desactivación o cambio de rol)
    // nadie más puede entrar a arreglarlo.
    private function esUltimoAdministradorActivo(Usuario $usuario): bool
    {
        if ($usuario->rol !== 'Administrador' || $usuario->estado !== 'activo') {
            return false;
        }

        return Usuario::where('rol', 'Administrador')
            ->where('estado', 'activo')
            ->where('id', '!=', $usuario->id)
            ->doesntExist();
    }

    public function index()
    {
        $usuarios = Usuario::with('cuentaAcceso')->orderBy('apellido_paterno')->paginate(10);
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        // Cuentas de acceso (rol Administrador/Médico/Operador/Cuidador/Tutor)
        // que todavía no están vinculadas a ninguna ficha de personal.
        // Se excluye el rol Invitado porque es acceso temporal de feria, no personal.
        $cuentasDisponibles = User::whereDoesntHave('fichaPersonal')
            ->where('rol', '!=', 'Invitado')
            ->orderBy('name')
            ->get();

        return view('admin.usuarios.create', compact('cuentasDisponibles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'modo_cuenta'      => 'required|in:vincular,crear',
            'user_id'          => 'nullable|required_if:modo_cuenta,vincular|exists:users,id|unique:usuarios,user_id',
            'nombres'          => 'required|string|min:2|max:100|regex:/^\pL+(?:\s\pL+)*$/u',
            'apellido_paterno' => 'nullable|string|max:100|regex:/^\pL+(?:\s\pL+)*$/u|required_without:apellido_materno',
            'apellido_materno' => 'nullable|string|max:100|regex:/^\pL+(?:\s\pL+)*$/u|required_without:apellido_paterno',
            'ci'               => 'required|string|max:20|regex:/^[A-Za-z0-9\-]+$/|unique:usuarios,ci',
            'telefono'         => 'nullable|digits_between:7,15',
            'correo'           => 'required|email|max:255|unique:usuarios,correo',
            'direccion'        => 'nullable|string|max:255|regex:/^[\pL0-9#\.\,\-\/]+(?:\s[\pL0-9#\.\,\-\/]+)*$/u',
            'fecha_nacimiento' => 'required|date|after:1900-01-01|before_or_equal:-18 years',
            'rol'              => 'required|in:Administrador,Médico,Operador,Cuidador',
            'estado'           => 'required|in:activo,inactivo',
        ], [
            'fecha_nacimiento.before_or_equal' => 'El encargado debe ser mayor de 18 años para registrarse en el sistema.',
            'user_id.unique'                   => 'Esa cuenta de acceso ya está vinculada a otra ficha de personal.',
            'user_id.required_if'              => 'Selecciona la cuenta de acceso a vincular.',
        ]);

        $userId = $request->user_id;
        $passwordTemporal = null;

        // Si no se vincula una cuenta existente, se crea el login aquí mismo
        // (antes no había ninguna pantalla funcional para dar de alta cuentas
        // de Médico/Operador/Cuidador, solo el /register público, que estaba roto).
        if ($request->modo_cuenta === 'crear') {
            if (User::where('email', $request->correo)->exists()) {
                return back()->withErrors([
                    'correo' => 'Ya existe una cuenta de acceso con ese correo. Usa "Vincular cuenta existente".',
                ])->withInput();
            }

            $passwordTemporal = Str::password(12);

            $cuenta = User::create([
                'name'                   => trim("{$request->nombres} {$request->apellido_paterno} {$request->apellido_materno}"),
                'email'                  => $request->correo,
                'telefono'               => $request->telefono,
                'password'               => Hash::make($passwordTemporal),
                'rol'                    => $request->rol,
                'debe_cambiar_password'  => true,
            ]);

            $userId = $cuenta->id;
        }

        // El "Estado" de la ficha manda sobre si la cuenta puede iniciar
        // sesión — antes esto era solo un dato descriptivo sin efecto real.
        if ($userId) {
            User::whereKey($userId)->update(['activo' => $request->estado === 'activo']);
        }

        $nombreFoto = null;
        $archivo = $request->file('foto');
        if($archivo && $archivo->isValid()){
            $nombreFoto = time() . '_' . Str::random(20) . '.' . $archivo->getClientOriginalExtension();
            $archivo->move(public_path('images/usuarios'), $nombreFoto);
        }

        Usuario::create([
            'user_id'          => $userId,
            'nombres'          => $request->nombres,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'ci'               => $request->ci,
            'telefono'         => $request->telefono,
            'correo'           => $request->correo,
            'direccion'        => $request->direccion,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'rol'              => $request->rol,
            'estado'           => $request->estado,
            'foto'             => $nombreFoto,
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario registrado correctamente.')
            ->with('password_temporal', $passwordTemporal)
            ->with('correo_temporal', $passwordTemporal ? $request->correo : null);
    }

    public function show($id)
    {
        $usuario = Usuario::with('cuentaAcceso')->findOrFail($id);
        return view('admin.usuarios.show', compact('usuario'));
    }

    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);

        // Cuentas libres + la que ya está vinculada a esta ficha (si tiene una),
        // para que siga apareciendo seleccionada en el formulario.
        $cuentasDisponibles = User::where('rol', '!=', 'Invitado')
            ->where(function ($query) use ($usuario) {
                $query->whereDoesntHave('fichaPersonal')
                    ->orWhere('id', $usuario->user_id);
            })
            ->orderBy('name')
            ->get();

        return view('admin.usuarios.edit', compact('usuario', 'cuentasDisponibles'));
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'modo_cuenta'      => 'required|in:vincular,crear',
            'user_id'          => 'nullable|required_if:modo_cuenta,vincular|exists:users,id|unique:usuarios,user_id,' . $id,
            'nombres'          => 'required|string|min:2|max:100|regex:/^\pL+(?:\s\pL+)*$/u',
            'apellido_paterno' => 'nullable|string|max:100|regex:/^\pL+(?:\s\pL+)*$/u|required_without:apellido_materno',
            'apellido_materno' => 'nullable|string|max:100|regex:/^\pL+(?:\s\pL+)*$/u|required_without:apellido_paterno',
            'ci'               => 'required|string|max:20|regex:/^[A-Za-z0-9\-]+$/|unique:usuarios,ci,' . $id,
            'telefono'         => 'nullable|digits_between:7,15',
            'correo'           => 'required|email|max:255|unique:usuarios,correo,' . $id,
            'direccion'        => 'nullable|string|max:255|regex:/^[\pL0-9#\.\,\-\/]+(?:\s[\pL0-9#\.\,\-\/]+)*$/u',
            'fecha_nacimiento' => 'required|date|after:1900-01-01|before_or_equal:-18 years',
            'rol'              => 'required|in:Administrador,Médico,Operador,Cuidador',
            'estado'           => 'required|in:activo,inactivo',
        ], [
            'fecha_nacimiento.before_or_equal' => 'El encargado debe ser mayor de 18 años para registrarse en el sistema.',
            'user_id.unique'                   => 'Esa cuenta de acceso ya está vinculada a otra ficha de personal.',
            'user_id.required_if'              => 'Selecciona la cuenta de acceso a vincular.',
        ]);

        // Si esta ficha es el último Administrador activo, no se le puede
        // quitar el rol ni desactivar (se quedaría el sistema sin nadie que
        // pueda gestionarlo).
        $seguiraSiendoAdminActivo = $request->rol === 'Administrador' && $request->estado === 'activo';
        if (!$seguiraSiendoAdminActivo && $this->esUltimoAdministradorActivo($usuario)) {
            return back()->withErrors([
                'estado' => 'No puedes quitarle el rol de Administrador ni desactivar al último Administrador activo del sistema.',
            ])->withInput();
        }

        $userId = $usuario->user_id;
        $passwordTemporal = null;

        if ($request->modo_cuenta === 'crear' && !$usuario->user_id) {
            if (User::where('email', $request->correo)->exists()) {
                return back()->withErrors([
                    'correo' => 'Ya existe una cuenta de acceso con ese correo. Usa "Vincular cuenta existente".',
                ])->withInput();
            }

            $passwordTemporal = Str::password(12);

            $cuenta = User::create([
                'name'                   => trim("{$request->nombres} {$request->apellido_paterno} {$request->apellido_materno}"),
                'email'                  => $request->correo,
                'telefono'               => $request->telefono,
                'password'               => Hash::make($passwordTemporal),
                'rol'                    => $request->rol,
                'debe_cambiar_password'  => true,
            ]);

            $userId = $cuenta->id;
        } elseif ($request->modo_cuenta === 'vincular') {
            $userId = $request->user_id;
        }

        // Restablecer contraseña de una cuenta que ya existe (ej. la persona
        // perdió la temporal antes de su primer ingreso). Genera una nueva
        // temporal igual que al crear la cuenta, y obliga a cambiarla de nuevo.
        if ($request->boolean('restablecer_password') && $userId) {
            $passwordTemporal = Str::password(12);

            User::whereKey($userId)->update([
                'password'              => Hash::make($passwordTemporal),
                'debe_cambiar_password' => true,
            ]);
        }

        // El "Estado" de la ficha manda sobre si la cuenta puede iniciar
        // sesión — antes esto era solo un dato descriptivo sin efecto real.
        if ($userId) {
            User::whereKey($userId)->update(['activo' => $request->estado === 'activo']);
        }

        $nombreFoto = $usuario->foto;
        $archivo = $request->file('foto');
        if($archivo && $archivo->isValid()){
            if($usuario->foto){
                $fotoAnterior = public_path('images/usuarios/' . $usuario->foto);
                if(file_exists($fotoAnterior)) unlink($fotoAnterior);
            }
            $nombreFoto = time() . '_' . Str::random(20) . '.' . $archivo->getClientOriginalExtension();
            $archivo->move(public_path('images/usuarios'), $nombreFoto);
        }

        $usuario->update([
            'user_id'          => $userId,
            'nombres'          => $request->nombres,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'ci'               => $request->ci,
            'telefono'         => $request->telefono,
            'correo'           => $request->correo,
            'direccion'        => $request->direccion,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'rol'              => $request->rol,
            'estado'           => $request->estado,
            'foto'             => $nombreFoto,
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.')
            ->with('password_temporal', $passwordTemporal)
            ->with('correo_temporal', $passwordTemporal ? $request->correo : null);
    }

    // Soft Delete
    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);

        if ($usuario->user_id && (int) $usuario->user_id === (int) auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia ficha.');
        }

        if ($this->esUltimoAdministradorActivo($usuario)) {
            return back()->with('error', 'No puedes eliminar al último Administrador activo del sistema.');
        }

        $usuario->delete();

        // Eliminar la ficha también desactiva el acceso de la cuenta
        // vinculada — antes la persona podía seguir logueándose igual.
        if ($usuario->user_id) {
            User::whereKey($usuario->user_id)->update(['activo' => false]);
        }

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    // Ver eliminados
    public function eliminados()
    {
        $usuarios = Usuario::onlyTrashed()->orderBy('apellido_paterno')->paginate(10);
        return view('admin.usuarios.eliminados', compact('usuarios'));
    }

    // Restaurar
    public function restaurar($id)
    {
        $usuario = Usuario::withTrashed()->findOrFail($id);
        $usuario->restore();

        // Reactiva la cuenta vinculada, respetando el "Estado" de la ficha.
        if ($usuario->user_id) {
            User::whereKey($usuario->user_id)->update(['activo' => $usuario->estado === 'activo']);
        }

        return redirect()->route('usuarios.eliminados')
            ->with('success', 'Usuario restaurado correctamente.');
    }

    // Hard Delete
    public function eliminarPermanente($id)
    {
        $usuario = Usuario::withTrashed()->findOrFail($id);

        if ($usuario->user_id && (int) $usuario->user_id === (int) auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia ficha.');
        }

        if ($this->esUltimoAdministradorActivo($usuario)) {
            return back()->with('error', 'No puedes eliminar al último Administrador activo del sistema.');
        }

        $usuario->forceDelete();

        return redirect()->route('usuarios.eliminados')
            ->with('success', 'Usuario eliminado permanentemente.');
    }

    // PDF
    public function exportarPdf()
    {
        $usuarios = Usuario::orderBy('apellido_paterno')->get();
        $pdf = Pdf::loadView('admin.usuarios.pdf', compact('usuarios'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('reporte_usuarios.pdf');
    }

    // Excel
    public function exportarExcel()
    {
        return Excel::download(new UsuariosExport, 'reporte_usuarios.xlsx');
    }
}