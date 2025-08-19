<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

use App\Models\Proyecto;
use App\Models\Tarea;
use App\Models\TareaTipo;
use App\Models\User;
use App\Models\InventarioMaterial;
use App\Models\Avance;
use App\Models\Cotizacion;
use App\Models\Finanza;
use App\Models\Entregables;
use App\Models\Festivos;

class ComercialController extends Controller
{
    public function index( ) 
    {
        $year = date('Y');
        $festivos = new Festivos;
        $festivos->festivos($year);
        $festivos->festivos($year+1);

        $hoy = Carbon::today();
        $title = 'Proyectos con Cupo Reservado';

        $items = Proyecto::when(Request('nombre_proyecto'), function ($query, $nombre_proyecto) { 
            return $query->whereRaw('LOWER(nombre_proyecto) LIKE LOWER(?)', ["%$nombre_proyecto%"]);
        })
        ->when(Request('nombre_cliente'), function ($query, $nombre_cliente) { 
            return $query->whereRaw('LOWER(nombre_cliente) LIKE LOWER(?)', ["%$nombre_cliente%"]);
        })
        ->whereNull('id_estado')
        ->orderBy('id', 'desc')
        ->paginate(10)
        ->appends(request()->query());

        $header = ['ID', 'Nombre Proyecto', 'Nombre Cliente', 'Ubicación', 'Direccion', 'Telefono', 'Opciones'];
        $departamentos = json_decode(file_get_contents(storage_path('json/jsonCityColombia.json')), true);
        return view('comercial.index', compact('title', 'items', 'festivos', 'departamentos', 'header'));
    }

    public function create() 
    {
        $anterior = url()->previous();
        session(['comercial_url' => $anterior]);
        return $this->form();
    }

    public function edit($id)
    {
        $anterior = url()->previous();
        session(['comercial_url' => $anterior]);
        return $this->form($id);
    }

    public function form($id = null)
    {
        $proyecto = $id ? Proyecto::find($id) : null;
        $colaUsers = User::where('id_rol', 3)
            ->where('activo', 1)
            ->get(['id', 'nombre_completo'])
            ->toArray();
    
        $title = $id ? 'Editar Proyecto' : 'Crear Proyecto';
        $departamentos = file_get_contents(storage_path('json/jsonCityColombia.json'));
        $ciudades = [];
        if ($id && $proyecto) {
            $departamentoIndex = intval($proyecto->departamento);
            $arayDtp = json_decode($departamentos, true);
            if (isset($arayDtp[$departamentoIndex]['ciudades'])) {
                $ciudades = $arayDtp[$departamentoIndex]['ciudades'];
            }
        }
        $entregables = Entregables::all()->toArray();
        $tipoDocs = Proyecto::$tipoDocumento;
        return view('comercial.create', compact('title', 'proyecto', 'colaUsers', 'departamentos', 'ciudades', 'tipoDocs', 'entregables'));
    }

    public function save(Request $req)
    {
dd($req->all());


        $data = $req->validate([
            'id' => 'nullable|integer',
            'nombre_proyecto' => ['required', 'string', 'max:200', Rule::unique('proyectos')->ignore($req->id, 'id')],
            'departamento' => 'required|integer',
            'ciudad' => 'required|integer',
            'direccion' => ['required', 'string', Rule::unique('proyectos')->ignore($req->id, 'id')],
            'nombre_cliente' => 'required|string|max:100',
            'telefono_cliente' => 'required|string|max:15',
            'cedula_cliente' => 'required|digits_between:6,15',
            'tipo_doc_cliente' => ['required', 'integer', Rule::in(array_keys(Proyecto::$tipoDocumento))],
            'dias_trabajo' => 'required|integer|min:1'
        ]); 

        $msg = ucfirst($req->id ? 'Proyecto editado con éxito' : 'Proyecto creado con éxito');
    
        try {
            DB::beginTransaction();
            Proyecto::Create($data);
            DB::commit();
            return redirect(session('comercial_url'))->with('success', $msg);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return back()->with('error', 'Error en la base de datos: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

}