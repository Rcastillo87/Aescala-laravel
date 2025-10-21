<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

use App\Models\Area;
use App\Models\Otrosi;
use App\Models\User;
use App\Models\Proyecto;

class OtrosiController extends Controller
{
    public function index( ) 
    {
        $title = 'Lista de Otro Si';
        $items = Otrosi::with(['proyecto', 'user_encargado'])
        ->whereHas('proyecto', function ($query) {
            if (request('nombre_proyecto')) {
                $query->where('nombre_proyecto', 'like', '%' . request('nombre_proyecto') . '%');
            }
        })
        ->whereHas('user_encargado', function ($query) {
            if (request('id_userSerch')) {
                $query->where('id', request('id_userSerch'));
            }
        })
        ->paginate(10)
        ->appends(request()->query());

        $user = User::where('id_rol', 3)->get()->toArray();
        $headers = ['Nombre Proyecto', 'En Cargado', 'Numero', 'Fecha de Creacion', 'Opciones'];
        return view('otrosi.index', compact( 'title', 'items', 'headers', 'user'));
    }

    public function create() 
    {
        $anterior = url()->previous();
        session(['otro_si_url' => $anterior]);
        return $this->form();
    }

    public function edit($id)
    {
        $anterior = url()->previous();
        session(['otro_si_url' => $anterior]);
        return $this->form($id);
    }

    public function form($id = null)
    {
        $otroSi = $id ? Otrosi::find($id) : null;
        $colaUsers = User::where('id_rol', 3)
            ->where('activo', 1)
            ->get(['id', 'nombre_completo'])
            ->toArray();

        $title = $id ? 'Editar Otro Si' : 'Crear Otro Si';

        $proyectos = Proyecto::whereIN('id_estado', [1, 3, 5])->get(['id', 'nombre_proyecto'])->toArray();
        return view('otrosi.create', compact('title', 'proyectos', 'colaUsers'));
    }


    public function save(Request $request) 
    {


    }

    public function otroSiPdf($id)
    {
        try {
            $otroSi = Otrosi::with(['proyecto', 'user_encargado', 'area_entregable', 'area_entregable.area'])->findOrFail($id);
            $data = $otroSi->otroSi;
            $pdf = PDF::loadView('otrosi.pdfOtroSi', $data);
            return $pdf->stream('otro-si-' . $otroSi->id . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error generando OTRO SÍ PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

}
