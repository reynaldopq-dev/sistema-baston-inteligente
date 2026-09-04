<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Baston;
use App\Models\Beneficiario;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\BastonesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class BastonController extends Controller
{
    public function index()
    {
        $bastones = Baston::with('beneficiario')->orderBy('codigo')->paginate(10);
        return view('admin.bastones.index', compact('bastones'));
    }

    public function create()
    {
        $beneficiarios = Beneficiario::orderBy('apellido_paterno')->get();
        return view('admin.bastones.create', compact('beneficiarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo'            => 'required|string|max:50|regex:/^[A-Za-z0-9\-]+$/|unique:bastones,codigo',
            'marca'             => 'required|string|max:100|regex:/^[\pL0-9\-]+(?:\s[\pL0-9\-]+)*$/u',
            'modelo'            => 'required|string|max:100|regex:/^[\pL0-9\-]+(?:\s[\pL0-9\-]+)*$/u',
            'numero_serie'      => 'required|string|max:50|regex:/^[A-Za-z0-9\-]+$/|unique:bastones,numero_serie',
            'fecha_adquisicion' => 'required|date|after:2000-01-01|before_or_equal:today',
            'estado'            => 'required|in:activo,inactivo,en_mantenimiento',
            'bateria'           => 'nullable|integer|min:0|max:100',
            'beneficiario_id'   => 'nullable|exists:beneficiarios,id',
        ]);

        $nombreFoto = null;

        $archivo = $request->file('foto');

        if($archivo && $archivo->isValid()){
            $nombreFoto = time() . '_' . Str::random(20) . '.' . $archivo->getClientOriginalExtension();
            $archivo->move(public_path('images/bastones'), $nombreFoto);
        }

        Baston::create([
            'codigo'            => $request->codigo,
            'marca'             => $request->marca,
            'modelo'            => $request->modelo,
            'numero_serie'      => $request->numero_serie,
            'fecha_adquisicion' => $request->fecha_adquisicion,
            'estado'            => $request->estado,
            'bateria'           => $request->bateria,
            'beneficiario_id'   => $request->beneficiario_id,
            'foto'              => $nombreFoto,
        ]);

        return redirect()->route('bastones.index')
            ->with('success', 'Bastón registrado correctamente.');
    }




    public function show($id)
    {
        $baston = Baston::with('beneficiario')->findOrFail($id);
        return view('admin.bastones.show', compact('baston'));
    }

    public function edit($id)
    {
        $baston = Baston::findOrFail($id);
        $beneficiarios = Beneficiario::orderBy('apellido_paterno')->get();
        return view('admin.bastones.edit', compact('baston', 'beneficiarios'));
    }

    public function update(Request $request, $id)
    {
        $baston = Baston::findOrFail($id);

        $request->validate([
            'codigo'            => 'required|string|max:50|regex:/^[A-Za-z0-9\-]+$/|unique:bastones,codigo,' . $id,
            'marca'             => 'required|string|max:100|regex:/^[\pL0-9\-]+(?:\s[\pL0-9\-]+)*$/u',
            'modelo'            => 'required|string|max:100|regex:/^[\pL0-9\-]+(?:\s[\pL0-9\-]+)*$/u',
            'numero_serie'      => 'required|string|max:50|regex:/^[A-Za-z0-9\-]+$/|unique:bastones,numero_serie,' . $id,
            'fecha_adquisicion' => 'required|date|after:2000-01-01|before_or_equal:today',
            'estado'            => 'required|in:activo,inactivo,en_mantenimiento',
            'bateria'           => 'nullable|integer|min:0|max:100',
            'beneficiario_id'   => 'nullable|exists:beneficiarios,id',
        ]);

        $nombreFoto = $baston->foto;

        if($request->hasFile('foto')){
            if($baston->foto){
                $fotoAnterior = public_path('images/bastones/' . $baston->foto);
                if(file_exists($fotoAnterior)){
                    unlink($fotoAnterior);
                }
            }
            $archivo = $request->file('foto');
            $nombreFoto = time() . '_' . Str::random(20) . '.' . $archivo->getClientOriginalExtension();
            $archivo->move(public_path('images/bastones'), $nombreFoto);
        }

        $baston->update([
            'codigo'            => $request->codigo,
            'marca'             => $request->marca,
            'modelo'            => $request->modelo,
            'numero_serie'      => $request->numero_serie,
            'fecha_adquisicion' => $request->fecha_adquisicion,
            'estado'            => $request->estado,
            'bateria'           => $request->bateria,
            'beneficiario_id'   => $request->beneficiario_id,
            'foto'              => $nombreFoto,
        ]);

        return redirect()->route('bastones.index')
            ->with('success', 'Bastón actualizado correctamente.');
    }

    // Soft Delete
    public function destroy($id)
    {
        $baston = Baston::findOrFail($id);
        $baston->delete();

        return redirect()->route('bastones.index')
            ->with('success', 'Bastón eliminado correctamente.');
    }

    // Ver eliminados
    public function eliminados()
    {
        $bastones = Baston::onlyTrashed()->with('beneficiario')->orderBy('codigo')->paginate(10);
        return view('admin.bastones.eliminados', compact('bastones'));
    }

    // Restaurar
    public function restaurar($id)
    {
        Baston::withTrashed()->findOrFail($id)->restore();

        return redirect()->route('bastones.eliminados')
            ->with('success', 'Bastón restaurado correctamente.');
    }

    // Hard Delete
    public function eliminarPermanente($id)
    {
        Baston::withTrashed()->findOrFail($id)->forceDelete();

        return redirect()->route('bastones.eliminados')
            ->with('success', 'Bastón eliminado permanentemente.');
    }
    //exportar pfd
    public function exportarPdf()
    {
        $bastones = Baston::with('beneficiario')->orderBy('codigo')->get();
        $pdf = Pdf::loadView('admin.bastones.pdf', compact('bastones'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('reporte_bastones.pdf');
    }
    //exportar exel
    public function exportarExcel()
    {
        return Excel::download(new BastonesExport, 'reporte_bastones.xlsx');
    }
}
