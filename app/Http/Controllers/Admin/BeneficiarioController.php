<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beneficiario;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\BeneficiariosExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;


class BeneficiarioController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $beneficiarios = Beneficiario::query()
            ->with('bastones')
            ->when($buscar, function ($query, $buscar) {
                $query->where('nombres', 'like', "%{$buscar}%")
                    ->orWhere('apellido_paterno', 'like', "%{$buscar}%")
                    ->orWhere('apellido_materno', 'like', "%{$buscar}%")
                    ->orWhere('ci', 'like', "%{$buscar}%");
            })
            ->orderBy('apellido_paterno')
            ->paginate(10);

        return view('admin.beneficiarios.index', compact('beneficiarios'));
    }
    public function create()
    {
        return view('admin.beneficiarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres'          => 'required|string|min:2|max:100|regex:/^\pL+(?:\s\pL+)*$/u',
            'apellido_paterno' => 'nullable|string|max:100|regex:/^\pL+(?:\s\pL+)*$/u|required_without:apellido_materno',
            'apellido_materno' => 'nullable|string|max:100|regex:/^\pL+(?:\s\pL+)*$/u|required_without:apellido_paterno',
            'ci'               => 'required|string|max:20|regex:/^[A-Za-z0-9\-]+$/|unique:beneficiarios,ci',
            'fecha_nacimiento' => 'required|date|after:1900-01-01|before_or_equal:today',
            'telefono'         => 'nullable|digits_between:7,15',
            'direccion'        => 'nullable|string|max:255|regex:/^[\pL0-9#\.\,\-\/]+(?:\s[\pL0-9#\.\,\-\/]+)*$/u',
            'diagnostico'      => 'required|in:Ceguera Total,Baja Visión,Ceguera Congénita,Ceguera Adquirida,Degeneración Macular',
            'estado'           => 'required|in:activo,inactivo',
            'foto'             => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $nombreFoto = null;

        if($request->hasFile('foto')){
            $archivo = $request->file('foto');
            $nombreFoto = time() . '_' . Str::random(20) . '.' . $archivo->getClientOriginalExtension();
            $archivo->move(public_path('images/beneficiarios'), $nombreFoto);
        }

        Beneficiario::create([
            'nombres'          => $request->nombres,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'ci'               => $request->ci,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'telefono'         => $request->telefono,
            'direccion'        => $request->direccion,
            'diagnostico'      => $request->diagnostico,
            'estado'           => $request->estado,
            'foto'             => $nombreFoto,
        ]);

        return redirect()->route('beneficiarios.index')
            ->with('success', 'Beneficiario registrado correctamente.');
    }

    public function show($id)
    {
        $beneficiario = Beneficiario::with('cuidadores')->findOrFail($id);
        return view('admin.beneficiarios.show', compact('beneficiario'));
    }

    public function edit($id)
    {
        $beneficiario = Beneficiario::findOrFail($id);

        return view('admin.beneficiarios.edit', compact('beneficiario'));
    }

    public function update(Request $request, $id)
    {
        $beneficiario = Beneficiario::findOrFail($id);

        $request->validate([
            'nombres'          => 'required|string|min:2|max:100|regex:/^\pL+(?:\s\pL+)*$/u',
            'apellido_paterno' => 'nullable|string|max:100|regex:/^\pL+(?:\s\pL+)*$/u|required_without:apellido_materno',
            'apellido_materno' => 'nullable|string|max:100|regex:/^\pL+(?:\s\pL+)*$/u|required_without:apellido_paterno',
            'ci'               => 'required|string|max:20|regex:/^[A-Za-z0-9\-]+$/|unique:beneficiarios,ci,' . $id,
            'fecha_nacimiento' => 'required|date|after:1900-01-01|before_or_equal:today',
            'telefono'         => 'nullable|digits_between:7,15',
            'direccion'        => 'nullable|string|max:255|regex:/^[\pL0-9#\.\,\-\/]+(?:\s[\pL0-9#\.\,\-\/]+)*$/u',
            'diagnostico'      => 'required|in:Ceguera Total,Baja Visión,Ceguera Congénita,Ceguera Adquirida,Degeneración Macular',
            'estado'           => 'required|in:activo,inactivo',
        ]);

        $nombreFoto = $beneficiario->foto;

        if($request->hasFile('foto')){
            $archivo = $request->file('foto');
            $extension = strtolower($archivo->getClientOriginalExtension());

            if(!in_array($extension, ['jpg', 'jpeg', 'png'])){
                return back()->withErrors(['foto' => 'La foto debe ser jpg, jpeg o png.'])->withInput();
            }

            if($archivo->getSize() > 2048 * 1024){
                return back()->withErrors(['foto' => 'La foto no debe exceder 2MB.'])->withInput();
            }

            if($beneficiario->foto){
                $fotoAnterior = public_path('images/beneficiarios/' . $beneficiario->foto);
                if(file_exists($fotoAnterior)){
                    unlink($fotoAnterior);
                }
            }

            $nombreFoto = time() . '_' . Str::random(20) . '.' . $archivo->getClientOriginalExtension();
            $archivo->move(public_path('images/beneficiarios'), $nombreFoto);
        }

        $beneficiario->update([
            'nombres'          => $request->nombres,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'ci'               => $request->ci,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'telefono'         => $request->telefono,
            'direccion'        => $request->direccion,
            'diagnostico'      => $request->diagnostico,
            'estado'           => $request->estado,
            'foto'             => $nombreFoto,
        ]);

        return redirect()->route('beneficiarios.index')
            ->with('success', 'Beneficiario actualizado correctamente.');
    }

    // Soft Delete
    public function destroy($id)
    {
        $beneficiario = Beneficiario::findOrFail($id);
        $beneficiario->delete();

        return redirect()->route('beneficiarios.index')
            ->with('success', 'Beneficiario eliminado correctamente.');
    }

    // Ver eliminados
    public function eliminados()
    {
        $beneficiarios = Beneficiario::onlyTrashed()->orderBy('apellido_paterno')->paginate(10);
        return view('admin.beneficiarios.eliminados', compact('beneficiarios'));
    }

    // Restaurar
    public function restaurar($id)
    {
        Beneficiario::withTrashed()->findOrFail($id)->restore();

        return redirect()->route('beneficiarios.eliminados')
            ->with('success', 'Beneficiario restaurado correctamente.');
    }

    // Hard Delete
    public function eliminarPermanente($id)
    {
        Beneficiario::withTrashed()->findOrFail($id)->forceDelete();

        return redirect()->route('beneficiarios.eliminados')
            ->with('success', 'Beneficiario eliminado permanentemente.');
    }

    //reportes dpf
    public function exportarPdf()
    {
        $beneficiarios = Beneficiario::orderBy('apellido_paterno')->get();
        $pdf = Pdf::loadView('admin.beneficiarios.pdf', compact('beneficiarios'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('reporte_beneficiarios.pdf');
    }
    //reportes exel
    public function exportarExcel()
    {
        return Excel::download(new BeneficiariosExport, 'reporte_beneficiarios.xlsx');
    }
}
