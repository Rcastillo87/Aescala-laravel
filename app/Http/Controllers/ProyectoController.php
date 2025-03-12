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
        $items = Proyecto::when(Request('nombre_proyecto'), function ($query, $nombre_proyecto) { 
            return $query->whereRaw('LOWER(nombre_proyecto) LIKE LOWER(?)', ["%$nombre_proyecto%"]);
        })
        ->when(Request('nombre_cliente'), function ($query, $nombre_cliente) { 
            return $query->whereRaw('LOWER(nombre_cliente) LIKE LOWER(?)', ["%$nombre_cliente%"]);
        })
        ->when(Request('id_estado'), function ($query, $id_estado) { 
            return $query->where('id_estado', $id_estado);
        })
        ->when(Request('id_user'), function ($query, $id_user) { 
            return $query->where('id_user', $id_user);
        })
        ->orderBy('id', 'desc')
        ->paginate(10);

        $userColab = User::where('id_rol', 3)->where('activo', 1)
        ->get(['id', 'nombre_completo'])
        ->map(fn($user) => ['id' => $user->id, 'nombre_completo' => $user->nombre_completo])
        ->toArray();

        $departamentos = json_decode(file_get_contents(storage_path('json/jsonCityColombia.json')), true);
        return view('proyecto.index', compact('title', 'items', 'estado', 'departamentos', 'userColab'));
    }

    public function create() 
    {
        return $this->form();
    }

    public function edit($id)
    {
        return $this->form($id);
    }

    public function form($id = null)
    {
        $proyecto = $id ? Proyecto::find($id) : null;
        $colaUsers = User::where('id_rol', 3)
            ->where('activo', 1)
            ->get(['id', 'nombre_completo'])
            ->map(fn($user) => ['id' => $user->id, 'nombre_completo' => $user->nombre_completo])
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
        return view('proyecto.create', compact('title', 'proyecto', 'colaUsers', 'departamentos', 'ciudades'));
    }

    public function save(Request $req)
    {
        $data = $req->validate([
            'id' => 'nullable|integer',
            'nombre_proyecto' => 'required|string|max:200',
            'departamento' => 'required|integer',
            'ciudad' => 'required|integer',
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

    public function editStatus(Request $request, $id)
    {
        $proyecto = Proyecto::findOrFail($id);
        $proyecto->id_estado = $request->estado;
        $proyecto->save();
        return response()->json(['success' => true, 'message' => 'Estado actualizado']);
    }

}