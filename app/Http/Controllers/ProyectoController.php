<?php

namespace App\Http\Controllers;

use App\Models\Avance;
use App\Models\Finanza;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Models\Proyecto;
use App\Models\Tarea;
use App\Models\TareaTipo;
use App\Models\User;

class ProyectoController extends Controller
{
    public function index( ) 
    {
        $title = 'Lista de Proyectos';
        $estado = Proyecto::$estado;
        $items = Proyecto::with(['tareas'])->when(Request('nombre_proyecto'), function ($query, $nombre_proyecto) { 
            return $query->whereRaw('LOWER(nombre_proyecto) LIKE LOWER(?)', ["%$nombre_proyecto%"]);
        })
        ->when(Request('nombre_cliente'), function ($query, $nombre_cliente) { 
            return $query->whereRaw('LOWER(nombre_cliente) LIKE LOWER(?)', ["%$nombre_cliente%"]);
        })
        ->when(Request('id_estado'), function ($query, $id_estado) { 
            return $query->where('id_estado', $id_estado);
        })
        ->when(Request('id_userSerch'), function ($query, $id_user) { 
            return $query->where('id_user', $id_user);
        })
        ->orderBy('id', 'desc')
        ->paginate(10);

        $userColab = User::where('id_rol', 3)->where('activo', 1)
        ->get(['id', 'nombre_completo'])
        ->toArray();

        $estadoTarea = Tarea::$estado;
        $tareaTipo = TareaTipo::get(['id', 'nombre_tarea'])->toArray();
        $departamentos = json_decode(file_get_contents(storage_path('json/jsonCityColombia.json')), true);

        $headerFinanzas = ['Ingresos o Egresos', 'Concepto', 'Valor', 'Fecha Creación'];
        $tipoFinanzas = Finanza::$tipo;

        $headerAvance = ['Avance', 'Fecha de Ejecucion', 'Fecha Guardado', 'Opciones'];

        return view('proyecto.index', compact('title', 'items', 'estado', 'departamentos', 'userColab', 'estadoTarea', 'tareaTipo', 'headerFinanzas', 'tipoFinanzas', 'headerAvance'));
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

    public function saveTarea (Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable|integer',
            'id_proyecto' => ['required', 'integer', Rule::exists('proyectos', 'id') ],
            'id_user' => ['required', 'integer', Rule::exists('users', 'id') ],
            'id_tarea_estado' => ['required', 'integer', Rule::in(array_keys(Tarea::$estado))],
            'id_tarea_tipo' => ['required', 'integer', Rule::exists('tarea_tipos', 'id') ],
            'descripccion' => 'required|string|max:255',
            'fec_inicio' => ['required', 'date', 'date_format:Y-m-d'],
            'fec_fin' => ['required', 'date', 'date_format:Y-m-d']
        ]);

        $msg = ucfirst($request->id ? 'Tarea editada' : "Tarea asignada" );

        try {
            DB::beginTransaction();
            Tarea::updateOrCreate(['id' => $data['id']], $data);
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

    public function editTarea ($id)
    {
        $tarea = Tarea::find($id);
        if($tarea){
            return response()->json([
                'status' => true,
                'message' => 'Lista de préstamos.',
                'data' => $tarea
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No se encontro tarea.'
            ], 404);
        }
    }

    public function listFinanzas()
    {
        try {
            $listFinanzas = Finanza::where('id_proyecto', Request('id'))->orderBy('id', 'desc')->paginate(10);
            return response()->json([
                'status' => true,
                'message' => 'Lista de préstamos.',
                'data' => $listFinanzas
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener la lista.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function savefinanza (Request $request)
    {
        $data = $request->validate([
            'id_proyecto_finanza' => ['required', 'integer', Rule::exists('proyectos', 'id') ],
            'tipo' => ['required', 'integer', Rule::in(array_keys(Finanza::$tipo))],
            'valor' => ['required', 'integer'],
            'concepto' => 'required|string|max:255'
        ]);

        $data['id_proyecto'] = $data['id_proyecto_finanza'];
        $msg = "Ingreso o Egreso creado";
        try {
            DB::beginTransaction();
            Finanza::Create($data);
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

    public function listAvances()
    {
        try {
            $listAvance = Avance::where('id_tarea', Request('id'))->orderBy('createdAt', 'desc')->paginate(10);
            return response()->json([
                'status' => true,
                'message' => 'Lista de avance.',
                'data' => $listAvance
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener la lista.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function saveAvance (Request $request) 
    {
        $data = $request->validate([
            'id_tarea_avance' => ['required', 'integer', Rule::exists('tareas', 'id') ],
            'fec_avance' => ['required', 'date', 'date_format:Y-m-d'],
            'avance' => 'required|string|max:255'
        ]);

        $data['id_tarea'] = $data['id_tarea_avance'];
        $msg = "Avance creado";
        try {
            DB::beginTransaction();
            Avance::Create($data);
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

    public function deleteAvance()
    {
        try {
            $avance = Avance::findOrFail(Request('id'));
            $avance->delete();
            return response()->json([
                'status' => true,
                'message' => 'Avance eliminada.',
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener la lista.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}