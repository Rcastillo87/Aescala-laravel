<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Models\Proyecto;
use App\Models\User;

class ProyectoController extends Controller
{

    public function index( ) 
    {
        $title = 'Lista de Proyectos';
        $estado = Proyecto::$estado;
        $items = Proyecto::when(Request('nombre_completo'), function ($query, $nombre_completo) { 
            return $query->whereRaw('LOWER(nombre_completo) LIKE LOWER(?)', ["%$nombre_completo%"]);
        })
        ->paginate(10);
        return view('proyecto.index', compact('title', 'items', 'estado'));
    }

    public function create( ) 
    {
        $proyecto = null;
        $colaUsers = User::where('id_rol', 3)->where('activo', 1)
        ->get(['id', 'nombre_completo'])
        ->map(fn($user) => ['id' => $user->id, 'nombre_completo' => $user->nombre_completo])
        ->toArray();
        $title = 'Crear Proyecto';
        $departamentos = file_get_contents(storage_path('json/jsonCityColombia.json'));
        return view('proyecto.create', compact('title', 'proyecto', 'colaUsers', 'departamentos'));
    }

    public function edit($id)
    {
        $proyecto = Proyecto::find($id);
        $colaUsers = User::where('id_rol', 3)->where('activo', 1)
        ->get(['id', 'nombre_completo'])
        ->map(fn($user) => ['id' => $user->id, 'nombre_completo' => $user->nombre_completo])
        ->toArray();
        $title = 'Ediar Proyecto';
        $departamentos = file_get_contents(storage_path('json/jsonCityColombia.json'));
        return view('proyecto.create', compact('title', 'proyecto', 'colaUsers', 'departamentos'));
    }

    public function save(Request $req)
    {
        $data = $req->validate([
            'id' => 'nullable|integer',
            'nombre_proyecto' => 'required|string|max:200',

            'departamento' => 'nullable',
            'ciudad' => 'nullable',

            'direccion' => 'required|string|min:0',
            'nombre_cliente' => 'required|string|max:100',
            'telefono_cliente' => 'required|string|max:15',
            'val_obra_blanca' => 'nullable|integer|min:0',
            'val_obra_blanca_materiales' =>'nullable|integer|min:0',
            'val_obra_carpinteria'  =>'nullable|integer|min:0',
            'val_carpinteria_materiales'  =>'nullable|integer|min:0',
            'pres_otros' =>'nullable|integer|min:0',
            'observacion' => 'nullable|string',
            'fec_inicio' => ['required', 'date', 'date_format:Y-m-d'],
            'fec_fin_estimado' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:fec_inicio'],
            'fec_fin_real' => ['nullable', 'date', 'date_format:Y-m-d'],
            'id_estado' => Rule::in(array_keys(Proyecto::$estado)),
            'id_user' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
        ]);

        dd($data);
    
        $msg = ucfirst($req->id ? 'Proyecto editado con éxito' : 'Proyecto creado con éxito');
    
        try {
            DB::beginTransaction();
            Proyecto::updateOrCreate(['id' => $data['id']], $data);
            DB::commit();
            return redirect()->route('proyecto.index')->with('success', $msg);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return back()->with('error', 'Error en la base de datos: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

}