<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Beneficiario;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Gestiona las "Personas Responsables": familiares/tutores que cuidan a un
 * beneficiario específico y necesitan iniciar sesión para ver su monitoreo.
 *
 * Son cuentas de la tabla 'users' (rol Tutor) — distintas del directorio de
 * personal del centro (tabla 'usuarios', ver UsuarioController). La relación
 * con los beneficiarios que tienen a su cargo vive en la tabla 'beneficiario_user'.
 */
class TutorController extends Controller
{
    public function index()
    {
        $tutores = User::where('rol', 'Tutor')
            ->with('beneficiariosACargo')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.tutores.index', compact('tutores'));
    }

    public function create()
    {
        $beneficiarios = Beneficiario::orderBy('apellido_paterno')->get();
        return view('admin.tutores.create', compact('beneficiarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|min:2|max:150|regex:/^\pL+(?:\s\pL+)*$/u',
            'email'           => 'required|email|max:255|unique:users,email',
            'telefono'        => 'nullable|digits_between:7,15',
            'password'        => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'beneficiarios'   => 'nullable|array',
            'beneficiarios.*' => 'exists:beneficiarios,id',
        ]);

        $tutor = User::create([
            'name'                  => $request->name,
            'email'                 => $request->email,
            'telefono'              => $request->telefono,
            'password'              => Hash::make($request->password),
            'rol'                   => 'Tutor',
            'debe_cambiar_password' => true,
        ]);

        $tutor->beneficiariosACargo()->sync($request->beneficiarios ?? []);

        Auditoria::create([
            'user_id'     => auth()->id(),
            'accion'      => 'creado',
            'modelo'      => 'Tutor',
            'modelo_id'   => $tutor->id,
            'descripcion' => "Creó a la persona responsable {$tutor->name} ({$tutor->email})",
            'ip'          => $request->ip(),
        ]);

        return redirect()->route('tutores.index')
            ->with('success', 'Persona responsable registrada correctamente.');
    }

    public function show($id)
    {
        $tutor = User::where('rol', 'Tutor')->with('beneficiariosACargo')->findOrFail($id);
        return view('admin.tutores.show', compact('tutor'));
    }

    public function edit($id)
    {
        $tutor = User::where('rol', 'Tutor')->findOrFail($id);
        $beneficiarios = Beneficiario::orderBy('apellido_paterno')->get();
        $beneficiariosAsignados = $tutor->beneficiariosACargo()->pluck('beneficiarios.id')->toArray();

        return view('admin.tutores.edit', compact('tutor', 'beneficiarios', 'beneficiariosAsignados'));
    }

    public function update(Request $request, $id)
    {
        $tutor = User::where('rol', 'Tutor')->findOrFail($id);

        $request->validate([
            'name'            => 'required|string|min:2|max:150|regex:/^\pL+(?:\s\pL+)*$/u',
            'email'           => 'required|email|max:255|unique:users,email,' . $id,
            'telefono'        => 'nullable|digits_between:7,15',
            'password'        => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
            'beneficiarios'   => 'nullable|array',
            'beneficiarios.*' => 'exists:beneficiarios,id',
        ]);

        $datos = [
            'name'     => $request->name,
            'email'    => $request->email,
            'telefono' => $request->telefono,
        ];

        if ($request->filled('password')) {
            // El admin le está poniendo una contraseña nueva: se trata como
            // temporal, igual que en el alta.
            $datos['password'] = Hash::make($request->password);
            $datos['debe_cambiar_password'] = true;
        }

        $tutor->update($datos);
        $tutor->beneficiariosACargo()->sync($request->beneficiarios ?? []);

        $campos = array_diff(array_keys($tutor->getChanges()), ['updated_at']);
        if (!empty($campos)) {
            Auditoria::create([
                'user_id'     => auth()->id(),
                'accion'      => 'actualizado',
                'modelo'      => 'Tutor',
                'modelo_id'   => $tutor->id,
                'descripcion' => "Actualizó a la persona responsable {$tutor->name} — campos: " . implode(', ', $campos),
                'ip'          => $request->ip(),
            ]);
        }

        return redirect()->route('tutores.index')
            ->with('success', 'Persona responsable actualizada correctamente.');
    }

    public function destroy($id)
    {
        $tutor = User::where('rol', 'Tutor')->findOrFail($id);

        if ($tutor->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $nombre = $tutor->name;
        $tutor->delete();

        Auditoria::create([
            'user_id'     => auth()->id(),
            'accion'      => 'eliminado',
            'modelo'      => 'Tutor',
            'modelo_id'   => $id,
            'descripcion' => "Eliminó a la persona responsable {$nombre}",
            'ip'          => request()->ip(),
        ]);

        return redirect()->route('tutores.index')
            ->with('success', 'Persona responsable eliminada correctamente.');
    }

    // Ver eliminados
    public function eliminados()
    {
        $tutores = User::onlyTrashed()->where('rol', 'Tutor')->orderBy('name')->paginate(10);
        return view('admin.tutores.eliminados', compact('tutores'));
    }

    // Restaurar
    public function restaurar($id)
    {
        $tutor = User::onlyTrashed()->where('rol', 'Tutor')->findOrFail($id);
        $tutor->restore();

        Auditoria::create([
            'user_id'     => auth()->id(),
            'accion'      => 'restaurado',
            'modelo'      => 'Tutor',
            'modelo_id'   => $tutor->id,
            'descripcion' => "Restauró a la persona responsable {$tutor->name}",
            'ip'          => request()->ip(),
        ]);

        return redirect()->route('tutores.eliminados')
            ->with('success', 'Persona responsable restaurada correctamente.');
    }

    // Hard Delete
    public function eliminarPermanente($id)
    {
        $tutor = User::onlyTrashed()->where('rol', 'Tutor')->findOrFail($id);

        if ($tutor->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $nombre = $tutor->name;
        $tutor->forceDelete();

        Auditoria::create([
            'user_id'     => auth()->id(),
            'accion'      => 'eliminado_permanente',
            'modelo'      => 'Tutor',
            'modelo_id'   => $id,
            'descripcion' => "Eliminó permanentemente a la persona responsable {$nombre}",
            'ip'          => request()->ip(),
        ]);

        return redirect()->route('tutores.eliminados')
            ->with('success', 'Persona responsable eliminada permanentemente.');
    }
}
