<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $headerCotizacion = ['Fec Creacion', 'Nombre Material', 'Cantidad', 'Val Unid', 'SubTotal', 'Opciones'];

        $materiales = InventarioMaterial::where('activo', 1) 
        ->get(['id','nombre_material', 'cantidad', 'valor_unidad', 'spanTipo', 'unidades', 'id_unidad', 'tipo', 'descripccion'])
        ->toArray();

        return view('proyecto.index', compact('title', 'items', 'estado', 'departamentos', 'userColab', 'estadoTarea', 'tareaTipo', 
            'headerFinanzas', 'tipoFinanzas', 'headerAvance', 'headerCotizacion', 'materiales', 'festivos', 'hoy'));
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
        //dd($req->all());

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
            'fec_fin_estimado' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:fec_inicio'],
            'dias_trabajo' => 'nullable|integer|min:1',
            'conFechaFin' => 'required|integer|in:0,1',
            'fec_fin_real' => ['nullable', 'date', 'date_format:Y-m-d'],
            'id_estado' => Rule::in(array_keys(Proyecto::$estado)),
            'id_user' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
        ]);

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
            'fec_fin' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:fec_inicio'],
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
        $datos = (new Despachos)->despachos(Request('id'));
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
        $datos = (new Despachos)->despachos(Request('id'), Request('codigo'));
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

    public function listaBalance()
    {
        try {
            $proyecto = Proyecto::find(Request('id'));
            $totales = Despachos::with('material')->where('id_proyecto', Request('id'))
            ->selectRaw("
                SUM(CASE WHEN tipo = 1 THEN cantidad * valor_unidad ELSE 0 END) as total_tipo_1,
                SUM(CASE WHEN tipo = 2 THEN cantidad * valor_unidad ELSE 0 END) as total_tipo_2
            ");

            $data['carpinteria']['presupuesto'] = $proyecto->val_obra_carpinteria;
            $data['carpinteria']['presupuestoMaterial'] = $proyecto->val_carpinteria_materiales;
            $data['carpinteria']['gastosDinero'] = Finanza::where('id_proyecto', Request('id'))->where('tipo', 2)->sum('valor');
            $totales = $totales->whereHas('material', function ($q) { $q->where('tipo', 2); })->first();
            $total_final = $totales->total_tipo_1 - $totales->total_tipo_2;
            $data['carpinteria']['gastosMaterial'] = $total_final;

            $data['obrablanca']['presupuesto'] = $proyecto->val_obra_blanca;
            $data['obrablanca']['presupuestoMaterial'] = $proyecto->val_obra_blanca_materiales;
            $data['obrablanca']['gastosDinero'] = Finanza::where('id_proyecto', Request('id'))->where('tipo', 3)->sum('valor');
            $totales = $totales->whereHas('material', function ($q) { $q->where('tipo', 1); })->first();
            $total_final = $totales->total_tipo_1 - $totales->total_tipo_2;
            $data['obrablanca']['gastosMaterial'] = $total_final;

            $data['otros']['presupuesto'] = $proyecto->pres_otros;
            $data['otros']['gastosDinero'] = Finanza::where('id_proyecto', Request('id'))->where('tipo', 4)->sum('valor');
            $totales = $totales->whereHas('material', function ($q) { $q->whereNull('tipo'); })->first();
            $total_final = $totales->total_tipo_1 - $totales->total_tipo_2;
            $data['otros']['gastosMaterial'] = $total_final;

            $data['global']['presupuesto'] = $proyecto->totalProyecto;
            $data['global']['abonos'] = Finanza::where('id_proyecto', Request('id'))->where('tipo', 1)->sum('valor');
            $data['global']['gastos'] = $data['carpinteria']['gastosDinero'] + $data['obrablanca']['gastosDinero'] + $data['otros']['gastosDinero'] +
                                        $data['carpinteria']['gastosMaterial'] + $data['obrablanca']['gastosMaterial'] + $data['otros']['gastosMaterial'];
            $data['global']['ganancia'] = $data['global']['abonos'] - $data['global']['gastos'];
            

            return response()->json([
                'status' => true,
                'message' => 'Lista de avance.',
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

}