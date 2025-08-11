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
use App\Models\Despachos;
use App\Models\Festivos;

class ProyectoController extends Controller
{
    public function index( ) 
    {
        $year = date('Y');
        $festivos = new Festivos;
        $festivos->festivos($year);
        $festivos->festivos($year+1);

        if(!Request('id_estado')){
            $est = [1,5]; 
        } else {
            $est[] = Request('id_estado');
        }

        if(Auth::user()->isnotColab){
            $cola = Request('id_userSerch');
        } else {
            $cola = Auth::user()->id;
        }

        $festivos = Festivos::pluck('date')->map(fn($date) => Carbon::parse($date)->toDateString())->toArray();
        $hoy = Carbon::today();
        $title = 'Lista de Proyectos';
        $estado = Proyecto::$estado;
        $items = Proyecto::with(['tareas', 'finanzas'])->when(Request('nombre_proyecto'), function ($query, $nombre_proyecto) { 
            return $query->whereRaw('LOWER(nombre_proyecto) LIKE LOWER(?)', ["%$nombre_proyecto%"]);
        })
        ->when(Request('nombre_cliente'), function ($query, $nombre_cliente) { 
            return $query->whereRaw('LOWER(nombre_cliente) LIKE LOWER(?)', ["%$nombre_cliente%"]);
        })
        ->when($est, function ($query, $id_estado) {
            return $query->whereIN('id_estado', $id_estado);
        })
        ->when($cola, function ($query, $id_user) { 
            return $query->where('id_user', $id_user);
        })
        ->orderBy('id', 'desc')
        ->paginate(10)
        ->appends(request()->query());

        $userColab = User::where('id_rol', 3)->where('activo', 1)
        ->get(['id', 'nombre_completo'])
        ->toArray();

        $estadoTarea = Tarea::$estado;
        $tareaTipo = TareaTipo::get(['id', 'nombre_tarea'])->toArray();
        $departamentos = json_decode(file_get_contents(storage_path('json/jsonCityColombia.json')), true);

        $headerFinanzas = ['Ingresos o Egresos', 'Concepto', 'Valor', 'Fecha Creación'];
        $tipoFinanzas = Finanza::$tipo;

        $headerAvance = ['Avance', 'Fecha de Ejecucion', 'Fecha Guardado', 'Opciones'];
        $headerCotizacion = ['Fec Creacion', 'Nombre Material', 'Cantidad', 'Val Unid', 'SubTotal', 'Opciones'];
        $headerComparativo = ['ID', 'Nombre Item', 'Cant. Desp.', 'Val Unidad Desp.', 'Cant. Cotizada', 'Val Unidad Cotizado'];

	    $materiales = InventarioMaterial::where('activo', 1)->get()->toArray();
        $proyecto = [];
        return view('proyecto.index', compact('title', 'items', 'estado', 'departamentos', 'userColab', 'estadoTarea', 'tareaTipo', 
            'headerFinanzas', 'tipoFinanzas', 'headerAvance', 'headerCotizacion', 'materiales', 'festivos', 'hoy', 'headerComparativo', 'proyecto'));
    }

    public function create() 
    {
        $anterior = url()->previous();
        session(['proyecto_url' => $anterior]);
        return $this->form();
    }

    public function edit($id)
    {
        $anterior = url()->previous();
        session(['proyecto_url' => $anterior]);
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
        if($req->conFechaFin == 0){
            $val = ['dias_trabajo' => 'nullable|integer|min:1']; 
        } else {
            $val = ['fec_fin_estimado' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:fec_inicio']]; 
        }

        $valbase = [
            'id' => 'nullable|integer',
            'nombre_proyecto' => ['required', 'string', 'max:200', Rule::unique('proyectos')->ignore($req->id, 'id')],
            'departamento' => 'required|integer',
            'ciudad' => 'required|integer',
            'direccion' => ['required', 'string', Rule::unique('proyectos')->ignore($req->id, 'id')],
            'nombre_cliente' => 'required|string|max:100',
            'telefono_cliente' => 'required|string|max:15',
            'observacion' => 'nullable|string',
            'fec_inicio' => ['required', 'date', 'date_format:Y-m-d'],
            'conFechaFin' => 'required|integer|in:0,1',
            'fec_fin_real' => ['nullable', 'date', 'date_format:Y-m-d'],
            'id_estado' => Rule::in(array_keys(Proyecto::$estado)),
            'id_user' => [
                    'required',
                    'integer',
                    Rule::exists('users', 'id'),
                ],
            'id_user_obra_blanca' => [
                    'nullable',
                    'integer',
                    Rule::exists('users', 'id'),
                ],
            'id_user_carpinteria' => [
                    'nullable',
                    'integer',
                    Rule::exists('users', 'id'),
                ]
            ]; 

        $data = $req->validate(array_merge($val, $valbase));

        if($data['conFechaFin']==1){
            $festivos = Festivos::pluck('date')->map(fn($date) => Carbon::parse($date)->toDateString())->toArray();
            $data['dias_trabajo'] = ceil( (new Festivos)
                ->contarDiasHabiles($data['fec_inicio'], $data['fec_fin_estimado'], $festivos) );
        } else {
            $data['fec_fin_estimado'] = (new Festivos)->calcularFechaFin($data['fec_inicio'], $data['dias_trabajo']);
        }

        $msg = ucfirst($req->id ? 'Proyecto editado con éxito' : 'Proyecto creado con éxito');
    
        try {
            DB::beginTransaction();
            Proyecto::updateOrCreate(['id' => $data['id']], $data);
            DB::commit();
            return redirect(session('proyecto_url'))->with('success', $msg);
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

        if ((int)$request->estado === 3) {
            if (!$request->filled('fecha_dua')) {
                return response()->json([
                    'success' => false,
                    'message' => 'La fecha de entrega (DUA) es obligatoria para este estado.'
                ], 422);
            }

            $proyecto->fec_fin_real = $request->fecha_dua;
            Tarea::where('id_proyecto', $id)
                ->whereIn('id_tarea_estado', [1, 2])
                ->update([
                    'fec_fin_real' => $request->fecha_dua,
                    'id_tarea_estado' => 3
                ]);

        } else {
            $proyecto->fec_fin_real = null;
        }

        $proyecto->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente.'
        ]);
    }

    public function saveTarea (Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable|integer',
            'id_proyecto' => ['required', 'integer', Rule::exists('proyectos', 'id') ],
            'id_user' => ['required', 'integer', Rule::exists('users', 'id') ],
            'id_tarea_estado' => ['required', 'integer', Rule::in(array_keys(Tarea::$estado))],
            'id_tarea_tipo' => ['required', 'integer', Rule::exists('tarea_tipos', 'id') ],
            'descripccion' => 'nullable|string|max:255',
            'fec_inicio' => ['required', 'date', 'date_format:Y-m-d'],
            'fec_fin' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:fec_inicio'],
            'fec_fin_real' => ['nullable', 'date', 'date_format:Y-m-d'],
            'conFechaFin' => 'required|integer|in:0,1',
            'dias_trabajo' => 'required|integer|min:1'
        ]);

        if($data['conFechaFin']==1){
            $festivos = Festivos::pluck('date')->map(fn($date) => Carbon::parse($date)->toDateString())->toArray();
            $data['dias_trabajo'] = ceil( (new Festivos)
                ->contarDiasHabiles($data['fec_inicio'], $data['fec_fin'], $festivos) );
        } else {
            $data['fec_fin'] = (new Festivos)->calcularFechaFin($data['fec_inicio'], $data['dias_trabajo']);
        }

        $msg = ucfirst($request->id ? 'Tarea editada' : "Tarea asignada" );

        try {
            DB::beginTransaction();
            Tarea::updateOrCreate(['id' => $data['id']], $data);
            DB::commit();
            return redirect()->back()->with('success', $msg);
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
                'message' => 'Lista de tareas.',
                'data' => $tarea
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No se encontro tarea.'
            ], 404);
        }
    }

    public function deleteTarea ()
    {
        $tarea = Tarea::find(Request('id'));
        $idPro = $tarea->id_proyecto;
        if($tarea){
            Avance::where('id_tarea', Request('id'))->delete();
            $tarea->delete();
            if (!Tarea::where('id_tarea_estado', 2)->where('id_proyecto', $idPro)->exists()) {
                $val = Tarea::where('id_tarea_estado', 3)->where('id_proyecto', $idPro)->orderBy('createdAt', 'desc')->first();
                if ($val) {
                    $val->update(['id_tarea_estado' => 2]);
                }
            }
            return response()->json([
                'status' => true,
                'message' => 'Se elimio la tarea y los avaces de esta.',
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
            $listFinanzas = Finanza::where('id_proyecto', Request('id'))->orderBy('fec_inicio', 'desc')->paginate(10);
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
            $id = Tarea::find(Request('id'));
            $listAvance = Tarea::with(['avance', 'tareaTipo'])
            ->where('id_proyecto', $id->id_proyecto)
            ->orderBy('fec_inicio', 'desc')
            ->get()
            ->map(function ($data) {
                return [
                    'id' => $data->id,
                    'nombre_tarea' => $data->tareaTipo->nombre_tarea,
                    'fech_ini' => $data->fec_inicio,
                    'fech_fin' => $data->fec_fin,
                    'estado' => $data->spanEstado,
                    'avances' => $data->avance
                ];
            })->toArray();

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
            return redirect()->back()->with('success', $msg);
            //return redirect()->route('proyecto.index')->with('success', $msg);
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

    public function listaCotizacion()
    {
        try {
            $list = Cotizacion::with(['material'])
            ->where('id_proyecto', Request('id'))
            ->selectRaw(
                'id,
                id_inventario,
                createdAt,
                cantidad,
                valor_unidad'
            )
            ->paginate(10)
            ->through(function ($data) {
                return [
                    'id' => $data->id,
                    'id_inventario' => $data->id_inventario,
                    'cantidad' => $data->cantidad,
                    'valor_unidad' => $data->valor_unidad,
                    'subtotal' => $data->cantidad * $data->valor_unidad,
                    'createdAt' => explode(' ', $data->createdAt)[0],
                    'nombre_material' => $data->material->nombre_material
                ];
            });

            return response()->json([
                'status' => true,
                'message' => 'Lista de avance.',
                'data' => $list
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener la lista.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function saveCotizacion(Request $request)
    {
        $validated = $request->validate([
            'id_proyecto_cotizacion' => [
                'nullable',
                'integer',
                Rule::exists('proyectos', 'id'),
            ],
            'materiales' => ['required', 'array', 'min:1'],
            'materiales.*.id_material' => [
                'required',
                'integer',
                Rule::exists('inventario_materiales', 'id'),
                function ($attribute, $value, $fail) {
                    $request = request();
                    $idProyecto = $request->input("id_proyecto_cotizacion");
                    $material = InventarioMaterial::find($value);
                    if (!$material || $material->activo != 1) {
                        $fail('El material seleccionado no está disponible.');
                    }
                    
                    $cot = Cotizacion::where('id_proyecto', $idProyecto)->where('id_inventario', $value)->first();
                    if ($cot) {
                        $name = $cot->material->nombre_material;
                        $fail("El material: $name, ya se encuentra cotizado.");
                    }
                }
            ],
            'materiales.*.cantidad' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $materialId = $request->input("materiales.{$index}.id_material");
                    $material = InventarioMaterial::find($materialId);
    
                    if ($material && $request->tipo != 2 && $value > $material->cantidad) {
                        $fail("La cantidad para {$material->nombre_material} excede el stock ({$material->cantidad}).");
                    }
                }
            ],
            'materiales.*.valor_unidad' => ['required', 'integer']
        ]);

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['materiales'] as $material) {
                    $dato['id_proyecto'] = $validated['id_proyecto_cotizacion'];
                    $dato['id_inventario'] = $material['id_material'];
                    $dato['cantidad'] = $material['cantidad'];
                    $dato['valor_unidad'] = $material['valor_unidad'];
                    Cotizacion::create($dato);
                }
            });
    
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Cotización guardada exitosamente']);
            }
    
            return redirect()->route('proyecto.index')->with('success', 'Cotización guardada exitosamente');
    
        } catch (\Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al guardar la cotización',
                    'error' => $e->getMessage()
                ], 500);
            }
    
            return back()->withErrors(['message' => 'Error al guardar la cotización']);
        }
    }

    public function deleteCotizacion()
    {
        try {
            $cot = Cotizacion::findOrFail(Request('id'));
            $cot->delete();
            return response()->json([
                'status' => true,
                'message' => 'Item en cotizacion eliminado.',
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

    public function listaDespachos()
    {
        try {
            $data = (new Despachos)->despachos(Request('id'));
            return response()->json([
                'status' => true,
                'message' => 'Lista de Despachos.',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener la lista.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function pdfDespachos()
    {
        $proyecto = Proyecto::find(Request('id'));
        $datos = (new Despachos)->despachos(Request('id'), null, 1);
        $datosFactura = [
            'empresa' => [
                'razon' => env('RAZON', 'AESCALA'),
                'nit' => env('NIT', '901.451.774-2'),
                'telefono' => env('TEL', '323-345-0903'),
                'direccion' => env('DIREC', 'Dirección: carrera 1d #46-63'),
                'logo' => public_path('img/logo.png')
            ],
            'despacho' => $datos,
            'proyecto' => $proyecto
        ];

        $pdf = Pdf::loadView('proyecto.factura', $datosFactura);
        if( Request('view') ){
            return $pdf->stream('factura-'.Request('id').'.pdf');
        }
        return $pdf->download('factura-'.Request('id').'.pdf');
    }

    public function pdfDespacho()
    {
        $proyecto = Proyecto::find(Request('id'));
        $datos = (new Despachos)->despachos(Request('id'), Request('codigo'), 1);
        $datosFactura = [
            'empresa' => [
                'razon' => env('RAZON', 'AESCALA'),
                'nit' => env('NIT', '901.451.774-2'),
                'telefono' => env('TEL', '323-345-0903'),
                'direccion' => env('DIREC', 'Dirección: carrera 1d #46-63'),
                'logo' => public_path('img/logo.png')
            ],
            'despacho' => $datos,
            'proyecto' => $proyecto
        ];

        $pdf = Pdf::loadView('proyecto.factura', $datosFactura);
        if( Request('view') ){
            return $pdf->stream('factura-'.Request('id').'.pdf');
        }
        return $pdf->download('factura-'.Request('id').'.pdf');
    }

    public function listComparativo()
    {

        $proyecto = Proyecto::with([
            'despachos.material', 
            'cotizacion.material'
        ])->find(Request('id'));
        
        if (!$proyecto) {
            return response()->json(['error' => 'Proyecto no encontrado'], 404);
        }
        
        $idDespachos = $proyecto->despachos->pluck('id_material')->filter()->unique()->toArray();
        $idCotizacion = $proyecto->cotizacion->pluck('id_inventario')->filter()->unique()->toArray();
        $todosLosIds = array_unique(array_merge($idDespachos, $idCotizacion));

        $materiales = InventarioMaterial::wherein('id', $todosLosIds)->get(['id', 'nombre_material']);

        $data = []; 
        foreach ($materiales as $key => $value) {
            $array = [];
            $despachos = Despachos::where('id_proyecto', request('id'))
            ->where('id_material', $value['id'])
            ->get();

            // Agrupar por tipo
            $grouped = $despachos->groupBy('tipo');

            // Obtener colección segura o vacía si no existe el tipo
            $tipo1 = $grouped->get(1, collect());
            $tipo2 = $grouped->get(2, collect());

            // Calcular cantidades y subtotales
            $tipo1_cantidad = $tipo1->sum('cantidad');
            $tipo1_subtotal = $tipo1->sum(fn($item) => $item->cantidad * $item->valor_unidad);

            $tipo2_cantidad = $tipo2->sum('cantidad');
            $tipo2_subtotal = $tipo2->sum(fn($item) => $item->cantidad * $item->valor_unidad);

            // Resultados
            $total_cantidad = $tipo1_cantidad - $tipo2_cantidad;
            $total_subtotal = $tipo1_subtotal - $tipo2_subtotal;
            $valor_unitario_promedio = $total_cantidad > 0 ? $total_subtotal / $total_cantidad : 0;
        
            $cotizaciones = Cotizacion::where('id_proyecto', Request('id'))->where('id_inventario', $value['id'])->first();

            $array['id_material'] = $value['id'];
            $array['nombre_material'] = $value['nombre_material'];
            $array['cot_cantidad'] = $cotizaciones?$cotizaciones['cantidad']:'0';
            $array['cot_valor'] = $cotizaciones?$cotizaciones['valor_unidad']:'0';
            $array['desp_cantidad'] = $total_cantidad;
            $array['desp_valor'] = $valor_unitario_promedio;

            $data[] = $array;
        }
        return response()->json([
            'status' => true,
            'message' => 'Lista de comparativp.',
            'data' => $data
        ], 200);
    }

}