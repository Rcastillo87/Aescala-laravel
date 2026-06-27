<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\Gate;

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
use App\Models\DiasNoLaboralos;

class ProyectoController extends Controller
{
    public function index()
    {
        Gate::authorize('proyecto.index');
        $year = date('Y');
        $festivos = new Festivos;
        $festivos->festivos($year);
        $festivos->festivos($year + 1);
        $perPage = request('per_page', 10);

        $tareatipo = TareaTipo::where('orden', '>',  0)
                        ->orderBy('orden', 'asc')
                        ->get(['id', 'orden', 'porcentage'])
                        ->toArray();

        $festivos = Festivos::pluck('date')->map(fn($date) => Carbon::parse($date)->toDateString())->toArray();
        $hoy = Carbon::today();
        $title = 'Lista de Proyectos';
        $estado = Proyecto::$estado;

        if (request('id_userSerch') || request('nombre_proyecto') || request('nombre_cliente') || request('id_estado')) {
            if (request('id_estado')) {
                $est = [request('id_estado')];
            } else {
                $est = [];
            }
        } else {
            $est = [1, 5];
        }

        $cola = Auth::user()->isnotColab ? request('id_userSerch') : Auth::user()->id;
        $contra = Auth::user()->isContratista ? Auth::user()->id : request('id_contratista');

        $query = Proyecto::with(['tareas', 'finanzas', 'entreProyecto', 'otro_si'])
            ->when(request('nombre_proyecto'), function ($query, $nombre_proyecto) {
                return $query->whereRaw('LOWER(nombre_proyecto) LIKE LOWER(?)', ["%$nombre_proyecto%"]);
            })
            ->when(request('nombre_cliente'), function ($query, $nombre_cliente) {
                return $query->whereRaw('LOWER(nombre_cliente) LIKE LOWER(?)', ["%$nombre_cliente%"]);
            })
            ->when(!empty($est), function ($query) use ($est) {
                return $query->whereIn('id_estado', $est);
            })
            ->when($cola, function ($query, $cola) {
                return $query->where('id_user', $cola);
            })
            ->when($contra, function ($query, $contra) {
                return $query->where(function ($subQuery) use ($contra) {
                    $subQuery->where('id_user_obra_blanca', $contra)
                            ->orWhere('id_user_diseno', $contra);
                });
            })
            ->when(request('mes_pro'), function ($query, $mesPro) {
                return $query->where('fec_fin_estimado', 'LIKE', "{$mesPro}%");
            })
            ->when(request('ubicacion') !== null, function ($query) {
                $query->where('ubicacion', request('ubicacion'));
            })
            ->whereNotNull('id_estado')
            ->orderBy('fec_inicio', 'desc');

        if (request('export') == 1) {
            return $this->exportExcel($query->get(), $estado);
        }

        $items = $query->paginate($perPage)->appends(request()->query());

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
        $tipoDoc = Proyecto::$tipoDocumento;
        $ubicacion = Proyecto::$ubicacion;

        $userColab = User::where('id_rol', 3)->where('activo', 1)
            ->get(['id', 'nombre_completo'])
            ->toArray();

        $userDiseno = User::where('id_rol', 10)->where('activo', 1)
            ->get(['id', 'nombre_completo'])
            ->toArray();

        $colaUsers = User::where('id_rol', 3)
            ->where('activo', 1)
            ->get(['id', 'nombre_completo'])
            ->toArray();

        $contraUsers = User::where('id_rol', 7)
            ->where('activo', 1)
            ->get(['id', 'nombre_completo'])
            ->toArray();

        $ubicacion = Proyecto::$ubicacion;

        return view('proyecto.index', compact(
            'title',
            'items',
            'estado',
            'departamentos',
            'userColab',
            'userDiseno',
            'estadoTarea',
            'tareaTipo',
            'tipoDoc',
            'colaUsers',
            'headerFinanzas',
            'tipoFinanzas',
            'headerAvance',
            'headerCotizacion',
            'materiales',
            'festivos',
            'hoy',
            'headerComparativo',
            'proyecto',
            'contraUsers',
            'ubicacion',
            'tareatipo',
            'ubicacion'
        ));
    }

    private function exportExcel($items, $estado)
    {
        $headers = [
            "Content-Type" => "application/vnd.ms-excel; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=proyectos_reporte.xls"
        ];

        return response()->stream(function () use ($items, $estado) {
            echo "\xEF\xBB\xBF"; // BOM UTF-8

            echo "<table border='1' style='border-collapse:collapse'>
                    <thead>
                        <tr style='background:#242e68;color:#fff;font-weight:bold'>
                            <th>Nombre Proyecto</th>
                            <th>Cliente</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
                            <th>Estado</th>
                            <th>Días Contrato</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Entrega Estimada</th>
                            <th>Residente</th>
                            <th>Diseñador</th>
                            <th>Cont. Obra Blanca</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody>";

            foreach ($items as $item) {
                echo "<tr>
                        <td>".e($item->nombre_proyecto)."</td>
                        <td>".e($item->nombre_cliente)."</td>
                        <td>".e($item->telefono_cliente)."</td>
                        <td>".e($item->direccion)."</td>
                        <td>".e($estado[$item->id_estado])."</td> 
                        <td>{$item->dias_contrato}</td>
                        <td>".e($item->fec_inicio)."</td>
                        <td>".e($item->fec_fin_estimado ?? 'N/A')."</td>

                        <td>".e($item->user?->nombre_completo ?? '--')."</td>
                        <td>".e($item->userDiseno?->nombre_completo ?? '--')."</td>
                        <td>".e($item->userOB?->nombre_completo ?? '--')."</td>
                        <td>".e($item->observacion)."</td>
                    </tr>";
            }
            echo "</tbody></table>";
        }, 200, $headers);
    }

    public function create()
    {
        Gate::authorize('proyecto.create');
        $anterior = url()->previous();
        session(['proyecto_url' => $anterior]);
        return $this->form();
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
        Gate::authorize('proyecto.save');
        if ($req->conFechaFin == 0) {
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
            ]
        ];

        $data = $req->validate(array_merge($val, $valbase));

        if ($data['conFechaFin'] == 1) {
            $festivos = Festivos::pluck('date')->map(fn($date) => Carbon::parse($date)->toDateString())->toArray();
            $data['dias_trabajo'] = ceil((new Festivos)
                ->contarDiasHabiles($data['fec_inicio'], $data['fec_fin_estimado'], $festivos));
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

    public function begin(Request $req)
    {
        Gate::authorize('proyecto.begin');

        $valbase = [
            'id_proyecto_begin' => [
                'required',
                'integer',
                Rule::exists('proyectos', 'id'),
            ],
            'id_user_proy' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'id_user_obra_blanca' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'id_user_diseno' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'observacion' => 'nullable|string',
            'fec_inicio_begin' => ['required', 'date', 'date_format:Y-m-d'],
            'conFechaDise' => 'required|integer|in:0,1',
            'ubicacion' => ['required', 'integer', Rule::in(array_keys(Proyecto::$ubicacion))],
            'user_carpinteria' => ['nullable', 'string']
        ];

        $arrayAttributes = [
            'id_proyecto_begin'      => 'proyecto',
            'id_user_proy'           => 'Residente',
            'id_user_obra_blanca'    => 'contratista de obra blanca',
            'id_user_diseno'         => 'diseñador encargado',
            'fec_inicio_begin'       => 'fecha de inicio del proyecto',
            'conFechaDise'           => 'con fecha de diseño',
            'observacion'            => 'observación',
            'ubicacion'            => 'ubicación',
        ];

        $data = $req->validate($valbase, [], $arrayAttributes);

        $pro = Proyecto::find($data['id_proyecto_begin']);

        if($data['conFechaDise'] == 0){
            $data['fec_fin_estimado'] = $this->recalcularFechaFin($data['id_proyecto_begin'], $data['fec_inicio_begin'], $pro->dias_contrato);
            $data['fecha_comision'] = $this->recalcularFechaFin($data['id_proyecto_begin'], $data['fec_inicio_begin'], ($pro->dias_contrato - 10));
            //(new Festivos)->calcularFechaFin($data['fec_inicio_begin'], $pro->dias_contrato); //  viejo calculo
            $data['dias_trabajo'] = $pro->dias_contrato;
            $data['fec_inicio'] = $data['fec_inicio_begin'];
        }

        $data['id_user'] = $data['id_user_proy'];

        if (($pro->id_estado == 2) && ($data['conFechaDise'] == 0)) {
            $data['id_estado'] = 1;
        } elseif ($data['conFechaDise'] == 1) {
            $data['id_estado'] = 7;
        } else {
            $data['id_estado'] = $pro->id_estado;
        }

        try {
            if(($data['conFechaDise'] == 1) && ($pro->id_estado == 2)) {
                $tarea =  new Tarea;
                $tarea['fec_inicio'] = $data['fec_inicio_begin'];
                $tarea['fec_fin'] = $this->recalcularFechaFin($data['id_proyecto_begin'], $data['fec_inicio_begin'], 10);
                //(new Festivos)->calcularFechaFin($data['fec_inicio_begin'], 10);
                $tarea['id_proyecto'] = $data['id_proyecto_begin'];
                $tarea['id_user'] = $data['id_user'];
                $tarea['id_tarea_estado'] = 2;
                $tarea['descripccion'] = null;
                $tarea['id_tarea_tipo'] = 27;
                $tarea['dias_trabajo'] = 10;
                $tarea['save'] = 1;
                $tarea->save();
            }

            DB::beginTransaction();
            Proyecto::updateOrCreate(['id' => $data['id_proyecto_begin']], $data);
            DB::commit();
            return back()->with('success', 'Proyecto editado con éxito');
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
        Gate::authorize('proyecto.editStatus');
        $proyecto = Proyecto::findOrFail($id);
        $estado   = (int) $request->estado;

        // Estado 6 → reset
        if ($estado === 6) {
            $proyecto->update([
                'img_firma'   => null,
                'id_estado'   => null,
                'fec_fin_real' => null
            ]);
            return response()->json(['success' => true, 'message' => 'Se ha borrado la firma exitosamente.']);
        }

        // Estado 3 → requiere fecha_dua
        if ($estado === 3) {
            if (!$request->filled('fecha_dua')) {
                return response()->json([
                    'success' => false,
                    'message' => 'La fecha DUA es obligatoria para este estado.'
                ], 422);
            }

            $proyecto->fec_fin_real = $request->fecha_dua;
            Tarea::where('id_proyecto', $id)
                ->whereIn('id_tarea_estado', [1, 2])
                ->update([
                    'fec_fin_real'    => $request->fecha_dua,
                    'id_tarea_estado' => 3
                ]);
        } else {
            $proyecto->fec_fin_real = null;
        }

        $proyecto->id_estado = $estado;
        $proyecto->save();
        return response()->json(['success' => true, 'message' => 'Estado actualizado correctamente.']);
    }

    public function saveTarea(Request $request)
    {
        Gate::authorize('proyecto.saveTarea');
        $data = $request->validate([
            'id' => 'nullable|integer',
            'id_proyecto' => ['required', 'integer', Rule::exists('proyectos', 'id')],
            'id_user' => ['required', 'integer', Rule::exists('users', 'id')],
            'id_tarea_estado' => ['required', 'integer', Rule::in(array_keys(Tarea::$estado))],
            'id_tarea_tipo' => ['required', 'integer', Rule::exists('tarea_tipos', 'id')],
            'descripccion' => 'nullable|string|max:255',
            'fec_inicio' => ['required', 'date', 'date_format:Y-m-d'],
            'fec_fin' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:fec_inicio'],
            'fec_fin_real' => ['nullable', 'date', 'date_format:Y-m-d'],
            'conFechaFin' => 'required|integer|in:0,1',
            'dias_trabajo' => 'required|integer|min:1'
        ]);

        if ($data['conFechaFin'] == 1) {
            $festivos = Festivos::pluck('date')->map(fn($date) => Carbon::parse($date)->toDateString())->toArray();
            $data['dias_trabajo'] = ceil((new Festivos)
                ->contarDiasHabiles($data['fec_inicio'], $data['fec_fin'], $festivos));
        } else {
            $data['fec_fin'] = (new Festivos)->calcularFechaFin($data['fec_inicio'], $data['dias_trabajo']);
        }

        $msg = ucfirst($request->id ? 'Tarea editada' : "Tarea asignada");

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

    public function editTarea($id)
    {
        Gate::authorize('proyecto.editTarea');
        $tarea = Tarea::find($id);
        if ($tarea) {
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

    public function deleteTarea()
    {
        Gate::authorize('proyecto.deleteTarea');
        $tarea = Tarea::find(Request('id'));
        $idPro = $tarea->id_proyecto;
        if ($tarea) {
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

    public function listAvances()
    {
        Gate::authorize('proyecto.listAvances');
        try {
            $id = Tarea::find(Request('id'));
            $listAvance = Tarea::with([
                    'tareaTipo',
                    'avance' => function ($query) {
                        $query->orderBy('fec_avance', 'desc');
                    }
                ])
                ->where('id_proyecto', $id->id_proyecto)
                ->orderBy('createdAt', 'desc')
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

    public function saveAvance(Request $request)
    {
        Gate::authorize('proyecto.saveAvance');
        $data = $request->validate([
            'id_tarea_avance' => ['required', 'integer', Rule::exists('tareas', 'id')],
            'fec_avance' => ['required', 'date', 'date_format:Y-m-d'],
            'avance' => 'required|string|max:1000'
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
        Gate::authorize('proyecto.deleteAvance');
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

    public function listaDespachos()
    {
        Gate::authorize('proyecto.listaDespachos');
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
        Gate::authorize('proyecto.pdfDespachos');
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
            'proyecto' => $proyecto,
            'cotizacion' => false
        ];

        $pdf = Pdf::loadView('proyecto.factura', $datosFactura);
        $pdf->setPaper('letter', 'portrait');
        $pdf->set_option('isHtml5ParserEnabled', true);
        $pdf->set_option('isRemoteEnabled', true);
        $pdf->set_option('defaultFont', 'DejaVu Sans');

        if (Request('view')) {
            return $pdf->stream('factura-' . Request('id') . '.pdf');
        }
        return $pdf->download('factura-' . Request('id') . '.pdf');
    }

    public function pdfDespacho()
    {
        Gate::authorize('proyecto.pdfDespacho');
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
            'proyecto' => $proyecto,
            'cotizacion' => false
        ];

        $pdf = Pdf::loadView('proyecto.factura', $datosFactura);
        if (Request('view')) {
            return $pdf->stream('factura-' . Request('id') . '.pdf');
        }
        return $pdf->download('factura-' . Request('id') . '.pdf');
    }

    public function listComparativo()
    {
        Gate::authorize('proyecto.listComparativo');
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
            $array['cot_cantidad'] = $cotizaciones ? $cotizaciones['cantidad'] : '0';
            $array['cot_valor'] = $cotizaciones ? $cotizaciones['valor_unidad'] : '0';
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

    public function contratoPdf($idProyecto)
    {
        try {
            $proyecto = Proyecto::with('entreProyecto')->findOrFail($idProyecto);
            $data = $proyecto->contrato;

            // Generar PDF desde la vista HTML
            $pdf = PDF::loadView('proyecto.pdfContrato', $data);

            // Devolver el PDF para visualización en el navegador
            return $pdf->stream('contrato-' . $proyecto->id . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error generando contrato PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    public function excelDespachoProyecto($id)
    {
        Gate::authorize('proyecto.excelDespachoProyecto');
        try {

            $proyecto = Proyecto::find($id);

            if (!$proyecto) {
                return response()->json([
                    'message' => 'Proyecto no encontrado'
                ], 404);
            }

            $despachos = Despachos::with('material')
                ->where('id_proyecto', $id)
                ->get();

            if ($despachos->isEmpty()) {
                return response()->json([
                    'message' => 'El proyecto no tiene despachos para exportar'
                ], 404);
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            /* ===============================
            * TITULO (MISMO ESTILO)
            * =============================== */
            $sheet->mergeCells('A1:H1');
            $sheet->setCellValue(
                'A1',
                'REPORTE DE DESPACHOS – ' . $proyecto->nombre_proyecto
            );

            $sheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1F2937']
                ]
            ]);

            /* ===============================
            * SUBTITULO (MISMO ESTILO)
            * =============================== */
            $sheet->mergeCells('A2:H2');
            $sheet->setCellValue(
                'A2',
                'Fecha de generación: ' . now()->format('Y-m-d H:i')
            );

            $sheet->getStyle('A2')->applyFromArray([
                'font' => [
                    'italic' => true,
                    'size' => 10
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ]
            ]);

            /* ===============================
            * ENCABEZADOS (MISMO ESTILO)
            * =============================== */
            $headers = [
                'ID Material',
                'Material',
                'Código',
                'Cantidad',
                'Valor Inventario',
                'Valor Venta',
                'Tipo',
                'Fecha'
            ];

            $row = 4;
            $sheet->fromArray($headers, null, "A{$row}");

            $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '059669']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ]
                ]
            ]);

            /* ===============================
            * DATOS
            * =============================== */
            $row++;
            $tventa = 0;
            $tinven = 0;

            foreach ($despachos as $despacho) {

                $valorInventario = $despacho->valor_inventario * ($despacho->tipo == 1 ? 1 : -1);
                $valorVenta      = $despacho->valor_unidad     * ($despacho->tipo == 1 ? 1 : -1);

                $sheet->setCellValue("A{$row}", $despacho->material->id ?? '');
                $sheet->setCellValue("B{$row}", $despacho->material->nombre_material ?? '');
                $sheet->setCellValue("C{$row}", $despacho->codigo);
                $sheet->setCellValue("D{$row}", $despacho->cantidad);
                $sheet->setCellValue("E{$row}", $valorInventario);
                $sheet->setCellValue("F{$row}", $valorVenta);
                $sheet->setCellValue("G{$row}", Despachos::$tipo[$despacho->tipo] ?? '');
                $sheet->setCellValue("H{$row}", optional($despacho->createdAt)->format('Y-m-d'));

                $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_HAIR
                        ]
                    ]
                ]);

                $tinven += $valorInventario;
                $tventa += $valorVenta;
                $row++;
            }

            /* ===============================
            * FORMATO MONEDA (MISMO ESTILO)
            * =============================== */
            $sheet->getStyle("E5:E{$row}")
                ->getNumberFormat()
                ->setFormatCode('"$"#,##0');

            $sheet->getStyle("F5:F{$row}")
                ->getNumberFormat()
                ->setFormatCode('"$"#,##0');

            /* ===============================
            * TOTAL CONSOLIDADO (MISMO ESTILO)
            * =============================== */
            $sheet->mergeCells("A{$row}:D{$row}");
            $sheet->setCellValue("A{$row}", 'TOTAL CONSOLIDADO');
            $sheet->setCellValue("E{$row}", $tinven);
            $sheet->setCellValue("F{$row}", $tventa);

            $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                'font' => [
                    'bold' => true
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DCFCE7']
                ],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_THICK
                    ]
                ]
            ]);

            /* ===============================
            * AUTO SIZE
            * =============================== */
            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            return response()->streamDownload(
                function () use ($spreadsheet) {
                    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                    $writer->save('php://output');
                },
                "despachos_proyecto_{$proyecto->id}.xlsx",
                [
                    'Content-Type' =>
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]
            );

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al generar el Excel del proyecto',
                'error'   => $e->getMessage(),
                'line'    => $e->getLine()
            ], 500);
        }
    }

    public function excelDespachosGeneral()
    {
        Gate::authorize('proyecto.excelDespachosGeneral');
        try {

            $despachos = Despachos::with(['material', 'proyecto'])
                ->whereHas('proyecto', function ($query) {
                    $query->where('id_estado', 1);
                })
                ->get();

            if ($despachos->isEmpty()) {
                return response()->json([
                    'message' => 'No hay despachos para exportar'
                ], 404);
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            /* ===============================
            * TITULO
            * =============================== */
            $sheet->mergeCells('A1:I1');
            $sheet->setCellValue('A1', 'REPORTE GENERAL DE DESPACHOS');

            $sheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1F2937']
                ]
            ]);

            /* ===============================
            * SUBTITULO
            * =============================== */
            $sheet->mergeCells('A2:I2');
            $sheet->setCellValue('A2', 'Fecha de generación: ' . now()->format('Y-m-d H:i'));

            $sheet->getStyle('A2')->applyFromArray([
                'font' => [
                    'italic' => true,
                    'size' => 10
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ]
            ]);

            /* ===============================
            * ENCABEZADOS
            * =============================== */
            $headers = [
                'Proyecto',
                'ID Material',
                'Material',
                'Código',
                'Cantidad',
                'Valor Inventario',
                'Valor Venta',
                'Tipo',
                'Fecha'
            ];

            $row = 4;
            $sheet->fromArray($headers, null, "A{$row}");

            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '059669']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ]
                ]
            ]);

            /* ===============================
            * DATOS
            * =============================== */
            $row++;
            $tventa = 0;
            $tinven = 0;

            foreach ($despachos as $despacho) {

                $valorInventario = $despacho->valor_inventario * ($despacho->tipo == 1 ? 1 : -1);
                $valorVenta      = $despacho->valor_unidad     * ($despacho->tipo == 1 ? 1 : -1);

                $sheet->setCellValue("A{$row}", $despacho->proyecto->nombre_proyecto);
                $sheet->setCellValue("B{$row}", $despacho->material->id);
                $sheet->setCellValue("C{$row}", $despacho->material->nombre_material);
                $sheet->setCellValue("D{$row}", $despacho->codigo);
                $sheet->setCellValue("E{$row}", $despacho->cantidad);
                $sheet->setCellValue("F{$row}", $valorInventario);
                $sheet->setCellValue("G{$row}", $valorVenta);
                $sheet->setCellValue("H{$row}", Despachos::$tipo[$despacho->tipo] ?? '');
                $sheet->setCellValue("I{$row}", optional($despacho->createdAt)->format('Y-m-d'));

                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_HAIR
                        ]
                    ]
                ]);

                $tinven += $valorInventario;
                $tventa += $valorVenta;
                $row++;
            }

            $sheet->getStyle("F5:F{$row}")
                ->getNumberFormat()
                ->setFormatCode('"$"#,##0');

            $sheet->getStyle("G5:G{$row}")
                ->getNumberFormat()
                ->setFormatCode('"$"#,##0');

            /* ===============================
            * TOTAL CONSOLIDADO
            * =============================== */
            $sheet->mergeCells("A{$row}:E{$row}");
            $sheet->setCellValue("A{$row}", 'TOTAL CONSOLIDADO');
            $sheet->setCellValue("F{$row}", $tinven);
            $sheet->setCellValue("G{$row}", $tventa);

            $row0 = $row - 1;
            $sheet->getStyle("F{$row0}")
                ->getNumberFormat()
                ->setFormatCode('"$"#,##0');

            $sheet->getStyle("G{$row0}")
                ->getNumberFormat()
                ->setFormatCode('"$"#,##0');

            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'font' => [
                    'bold' => true
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DCFCE7']
                ],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_THICK
                    ]
                ]
            ]);

            /* ===============================
            * AUTO SIZE
            * =============================== */
            foreach (range('A', 'I') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            return response()->streamDownload(
                function () use ($spreadsheet) {
                    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                    $writer->save('php://output');
                },
                'excelDespachosGeneral.xlsx',
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]
            );

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al generar el Excel',
                'error'   => $e->getMessage(),
                'line'    => $e->getLine()
            ], 500);
        }
    }

    public function trataDatosPDF()
    {
        Gate::authorize('proyecto.trataDatosPDF');
        try {
            $proyecto = Proyecto::find(Request('id'));
            $data = $proyecto->trataDatos;

            $pdf = Pdf::loadView('proyecto.trataDatosPDF', $data);
            return $pdf->stream('trataDatosPDF-' . $proyecto->id . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error generando contrato PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    public function actaEntregaPdf($idProyecto)
    {
        try {
            $proyecto = Proyecto::with('entreProyecto.entregable')->findOrFail($idProyecto);
            $data = $proyecto->acta_entrega;

            $pdf = PDF::loadView('proyecto.pdfActaEntrega', $data);
            return $pdf->stream('acta-entrega-' . $proyecto->id . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function calendarioProyecto(Request $request)
    {
        Gate::authorize('proyecto.calendarioProyecto');
        $request->validate(['id_proyecto' => 'required|integer']);

        $proyecto    = Proyecto::findOrFail($request->id_proyecto);
        $inicio      = Carbon::parse($proyecto->fec_inicio)->startOfDay();
        $finEstimado = Carbon::parse($proyecto->fec_fin_estimado)->startOfDay();

        $festivosGlobales = Festivos::whereBetween('date', [
            $inicio->toDateString(),
            $finEstimado->toDateString(),
        ])->get()->keyBy(fn($f) => Carbon::parse($f->date)->toDateString());

        $noLaborados = DiasNoLaboralos::where('id_proyecto', $proyecto->id)
            ->get()->keyBy('dia');

        $hoy   = Carbon::today();
        $meses = [];
        $cursor = $inicio->copy();

        while ($cursor->lte($finEstimado)) {
            $mesKey = $cursor->format('Y-m');

            if (!isset($meses[$mesKey])) {
                // Offset = día de semana del primer día VISIBLE en este mes.
                // Primer mes del proyecto → desde fec_inicio.
                // Meses siguientes → desde el día 1 del mes (se muestran completos).
                $esPrimerMes = ($cursor->month === $inicio->month && $cursor->year === $inicio->year);
                $primerDiaVisible = $esPrimerMes
                    ? $cursor->copy()
                    : Carbon::create($cursor->year, $cursor->month, 1);

                $meses[$mesKey] = [
                    'key'    => $mesKey,
                    'nombre' => $this->nombreMes($cursor->month) . ' ' . $cursor->year,
                    'offset' => $primerDiaVisible->dayOfWeek,
                    'dias'   => [],
                ];
            }

            $dateStr = $cursor->toDateString();
            $dow     = $cursor->dayOfWeek;
            $regFest = $festivosGlobales->get($dateStr);
            $regNL   = $noLaborados->get($dateStr);

            $meses[$mesKey]['dias'][] = [
                'date'              => $dateStr,
                'day'               => $cursor->day,
                'dayName'           => $this->nombreDia($dow),
                'dayOfWeek'         => $dow,
                'tipo'              => $this->tipoDia($dow, $regFest, $regNL),
                'estado'            => $this->estadoDia($cursor, $hoy),
                'festivoName'       => $regFest
                    ? ($this->esNoLaboralGlobal($regFest) ? ($regFest->comentario ?? $regFest->name) : $regFest->name)
                    : null,
                'noLaboradoDetalle' => $regNL ? $regNL->detalle : null,
                'editable'          => $this->esEditable($dow, $regFest, $cursor, $hoy),
                'noLaborado'        => $regNL !== null,
            ];

            $cursor->addDay();
        }

        return response()->json([
            'id_proyecto'  => $proyecto->id,
            'nombre'       => $proyecto->nombre_proyecto,
            'cliente'      => $proyecto->nombre_cliente,
            'fec_inicio'   => $inicio->toDateString(),
            'fec_fin_est'  => $finEstimado->toDateString(),
            'dias_trabajo' => $proyecto->dias_trabajo,
            'meses'        => array_values($meses),
        ]);
    }

    // ─── AJAX: marcar día no laborado ─────────────────────────────────
    public function saveDiaNoLaborado(Request $request)
    {
        Gate::authorize('proyecto.saveDiaNoLaborado');

        $request->validate([
            'id_proyecto'  => 'required|integer',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
            'detalle'      => 'required|string|max:500',
        ]);

        $proyecto = Proyecto::findOrFail($request->id_proyecto);

        $inicio = Carbon::parse($request->fecha_inicio)->startOfDay();
        $fin    = Carbon::parse($request->fecha_fin)->startOfDay();
        $hoy    = Carbon::today();

        // No permitir fechas futuras
        if ($inicio->gt($hoy) || $fin->gt($hoy)) {

            return response()->json([
                'error' => 'Solo se permiten fechas pasadas o actuales.'
            ], 422);
        }

        // Festivos/globales
        $festivos = Festivos::whereBetween('date', [
                $inicio->toDateString(),
                $fin->toDateString()
            ])
            ->get()
            ->keyBy(fn($f) => Carbon::parse($f->date)->toDateString());

        // Días ya registrados
        $diasExistentes = DiasNoLaboralos::where('id_proyecto', $proyecto->id)
            ->whereBetween('dia', [
                $inicio->toDateString(),
                $fin->toDateString()
            ])
            ->pluck('dia')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        $diasGuardar = [];

        $cursor = $inicio->copy();

        while ($cursor->lte($fin)) {

            $fecha = $cursor->toDateString();

            // Ya existe → ERROR
            if (in_array($fecha, $diasExistentes)) {

                return response()->json([
                    'error' => "El día {$fecha} ya fue marcado como no laborado."
                ], 422);
            }

            $esDomingo = $cursor->dayOfWeek === Carbon::SUNDAY;

            $esFestivo = isset($festivos[$fecha]);

            // Domingos y festivos/globales se IGNORAN
            if (!$esDomingo && !$esFestivo) {

                $diasGuardar[] = $fecha;
            }

            $cursor->addDay();
        }

        // No hubo días válidos
        if (empty($diasGuardar)) {

            return response()->json([
                'error' => 'No hay días válidos para registrar en el rango seleccionado.'
            ], 422);
        }

        // Guardar
        foreach ($diasGuardar as $fecha) {

            DiasNoLaboralos::create([
                'id_proyecto' => $proyecto->id,
                'dia'         => $fecha,
                'detalle'     => $request->detalle,
            ]);
        }

        // Recalcular fechas
        $nuevaFin = $this->recalcularFechaFin(
            $proyecto->id,
            $proyecto->fec_inicio,
            $proyecto->dias_contrato
        );

        $nuevaComi = $this->recalcularFechaFin(
            $proyecto->id,
            $proyecto->fec_inicio,
            ($proyecto->dias_contrato - 10)
        );

        $proyecto->fec_fin_estimado = $nuevaFin;
        $proyecto->fecha_comision   = $nuevaComi;
        $proyecto->save();

        return response()->json([
            'message'          => 'Días registrados correctamente.',
            'dias_guardados'   => count($diasGuardar),
            'nueva_fecha_fin'  => $nuevaFin->toDateString(),
        ]);
    }

    public function deleteDiaNoLaborado(Request $request)
    {
        Gate::authorize('proyecto.saveDiaNoLaborado');

        $request->validate([
            'id_proyecto' => 'required|integer',
            'dia'         => 'required|date',
        ]);

        $proyecto = Proyecto::findOrFail($request->id_proyecto);

        $deleted = DiasNoLaboralos::where('id_proyecto', $proyecto->id)
            ->where('dia', $request->dia)
            ->delete();

        if (!$deleted) {

            return response()->json([
                'error' => 'El día no existe.'
            ], 422);
        }

        // Recalcular fecha final
        $nuevaFin = $this->recalcularFechaFin(
            $proyecto->id,
            $proyecto->fec_inicio,
            $proyecto->dias_contrato
        );

        // Recalcular comisión
        $nuevaComi = $this->recalcularFechaFin(
            $proyecto->id,
            $proyecto->fec_inicio,
            ($proyecto->dias_contrato - 10)
        );

        $proyecto->fec_fin_estimado = $nuevaFin;
        $proyecto->fecha_comision   = $nuevaComi;

        $proyecto->save();

        return response()->json([
            'message' => 'Día removido correctamente.',
            'nueva_fecha_fin' => $nuevaFin->toDateString(),
        ]);
    }

    // ─── Recalcular fecha fin ──────────────────────────────────────────
    private function recalcularFechaFin($id, $fec_inicio, $dias_contrato): Carbon
    {
        $inicio       = Carbon::parse($fec_inicio)->startOfDay();

        $diasObjetivo = (float) $dias_contrato;

        $festivos = Festivos::where('date', '>=', $inicio->toDateString())
            ->pluck('date')->map(fn($d) => Carbon::parse($d)->toDateString())->toArray();

        $noLabs = DiasNoLaboralos::where('id_proyecto', $id)
            ->pluck('dia')->map(fn($d) => Carbon::parse($d)->toDateString())->toArray();

        $cursor = $inicio->copy();
        $contados = 0.0;

        while (true) {
            $dow  = $cursor->dayOfWeek;
            $date = $cursor->toDateString();

            if ($dow !== Carbon::SUNDAY && !in_array($date, $festivos) && !in_array($date, $noLabs)) {
                $contados += $dow === Carbon::SATURDAY ? 0.5 : 1.0;
            }

            if ($contados >= $diasObjetivo) break;
            $cursor->addDay();
        }

        return $cursor;
    }

    // ─── Helpers ──────────────────────────────────────────────────────
    private function tipoDia(int $dow, $regFest, $regNL): string
    {
        if ($regNL)   return 'no_laborado';
        if ($regFest) return $this->esNoLaboralGlobal($regFest) ? 'no_laboral_global' : 'festivo';
        if ($dow === Carbon::SUNDAY)   return 'domingo';
        if ($dow === Carbon::SATURDAY) return 'sabado';
        return 'laborable';
    }

    private function estadoDia(Carbon $fecha, Carbon $hoy): string
    {
        if ($fecha->isToday()) return 'hoy';
        if ($fecha->lt($hoy))  return 'pasado';
        return 'futuro';
    }

    private function esNoLaboralGlobal($reg): bool
    {
        return $reg && strtolower($reg->name) === 'día no labora';
    }

    private function esEditable(int $dow, $regFest, Carbon $fecha, Carbon $hoy): bool
    {
        return !$fecha->gt($hoy) && $dow !== Carbon::SUNDAY && !$regFest;
    }

    private function nombreDia(int $dow): string
    {
        return ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'][$dow];
    }

    private function nombreMes(int $m): string
    {
        return ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
                'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'][$m];
    }

}
