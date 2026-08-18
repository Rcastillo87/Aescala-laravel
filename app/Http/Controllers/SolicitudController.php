<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\SolicitudMaterial;
use App\Models\InventarioMaterial;
use App\Models\Cotizacion;
use App\Models\Proyecto;
use App\Models\SolicitudItems;
use App\Models\User;
use App\Models\Despachos;
use App\Http\Requests\SaveSolicitudRequest;
use App\Traits\RegistraBitacora;
use Illuminate\Support\Facades\Gate;

class SolicitudController extends Controller
{
    use RegistraBitacora;

    public function index( )
    {
        Gate::authorize('solicitud.index');
        if(Auth::user()->isAdmin || Auth::user()->isAnalista || Auth::user()->isAlmacenista){
            $cola = Request('id_userSerch');
        } else {
            $cola = Auth::user()->id;
        }

        $estadosItems = SolicitudItems::$estados;

        $title = 'Lista de Solicitud de Material';
        $query = SolicitudMaterial::with(['proyecto', 'usuario', 'cotizacion'])
            ->when(request('nombre_proyecto'), function ($query, $nombre_proyecto) {
                $query->whereHas('proyecto', function ($q) use ($nombre_proyecto) {
                    $q->whereRaw('LOWER(nombre_proyecto) LIKE ?', ['%' . strtolower($nombre_proyecto) . '%']);
                });
            })
            ->when($cola, function ($query, $id_user) {
                $query->where('id_user', $id_user);
            })
            ->when(request('id_estado'), function ($query, $id_estado) {
                $query->where('estado', $id_estado);
            })
            ->when(request()->filled('id_estado_item'), function ($query) use ($estadosItems ) {
                $id_estado_item = request('id_estado_item') + 1;
                $query->whereHas('items', function ($q) use ($id_estado_item, $estadosItems) {
                    $map = [
                        5 => fn($q) => $q->where('aprobado', 0),
                        6 => fn($q) => $q->where('aprobado', 1)->whereNotNull('id_user_aprueba'),
                    ];
                    if (array_key_exists($id_estado_item, $estadosItems)) {
                        $q->where('estado', $id_estado_item);
                    } elseif (isset($map[$id_estado_item])) {
                        $map[$id_estado_item]($q);
                    }
                });
            })
            ->when(request('departamento') !== null, function ($query) {
                $query->whereHas('proyecto', function ($q) {
                    $q->where('departamento', request('departamento'))
                    ->when(request('ciudad') !== null, function ($q2) {
                        $q2->where('ciudad', request('ciudad'));
                    });
                });
            })
            ->when(request('ubicacion') !== null, function ($query) {
                $query->whereHas('proyecto', function ($q) {
                    $q->where('ubicacion', request('ubicacion'));
                });
            })
            ->orderBy('id', 'desc');


        $ubicacion = Proyecto::$ubicacion;
        $departamentos = file_get_contents(storage_path('json/jsonCityColombia.json'));

        if (request('export') == 1) {
            return $this->exportExcel($query->get(), $ubicacion, $departamentos);
        }

        $items = $query->paginate(10)->appends(request()->query());

        $proyecto = Proyecto::with('tareas')
            ->when($cola, function ($query, $id_user) {
                $query->where('id_user', $id_user);
            })
            ->where('id_estado', [1, 5, 3])
            ->get();

        $estados = SolicitudMaterial::$estados;
        $userColab = User::whereIn('id_rol', [3, 9, 7])->where('activo', 1)->get(['id', 'nombre_completo'])->toArray();


        $headers = ['Nombre del Proyecto', 'Ubicación', 'Quien Solicito', 'Fecha de solicitud', 'Entregados y Faltantes', 'Estado Solicitud', 'Estado Items', 'Opciones'];
        if(!(Auth::User()->isAdmin || Auth::user()->isAnalista || Auth::user()->isAlmacenista)) {
            $headers = ['Nombre del Proyecto', 'Ubicación', 'Fecha de solicitud', 'Entregados y Faltantes', 'Estado', 'Observacion', 'Opciones'];
        }
        return view('solicitud.index', compact('title', 'items', 'headers', 'proyecto', 'estados', 'estadosItems', 'userColab', 'ubicacion', 'departamentos'));
    }

    private function exportExcel($items, $ubicacion, $departamentos)
    {
        $headers = [
            "Content-Type" => "application/vnd.ms-excel; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=reporte_solicitudes.xls"
        ];

        $depts = json_decode($departamentos, true);

        return response()->stream(function () use ($items, $ubicacion, $depts) {
            echo "\xEF\xBB\xBF"; // BOM UTF-8 para mantener tildes

            echo "<table border='1' style='border-collapse:collapse'>
                    <thead>
                        <tr style='background:#242e68;color:#fff;font-weight:bold'>
                            <th>Nombre del Proyecto</th>
                            <th>Ubicación</th>
                            <th>Quien Solicitó</th>
                            <th>Fecha de solicitud</th>
                            <th>Entregados y Faltantes</th>
                            <th>Estado Solicitud</th>
                        </tr>
                    </thead>
                    <tbody>";

            foreach ($items as $item) {
                // 2. CORRECCIÓN: Armamos la ubicación de forma segura idéntica a tu vista
                $nombreDept = 'N/A';
                $nombreCiudad = 'N/A';
                
                if ($item->proyecto) {
                    $idDept = intval($item->proyecto->departamento);
                    $idCiudad = intval($item->proyecto->ciudad);
                    
                    $nombreDept = $depts[$idDept]['departamento'] ?? 'N/A';
                    $nombreCiudad = $depts[$idDept]['ciudades'][$idCiudad] ?? 'N/A';
                }
                
                $nombreUbicacion = $item->proyecto?->ubicacion !== null 
                    ? ($ubicacion[$item->proyecto->ubicacion] ?? 'N/A') 
                    : 'N/A';

                // Concatenamos limpiamente usando interpolación de variables
                $ubicacionCompleta = "{$nombreDept} - {$nombreCiudad} - {$nombreUbicacion}";

                // Extraemos los datos calculados según tu modelo
                $entregados = $item->total_items_entregado ?? 0;
                $faltantes = $item->total_items_solicitud ?? 0; 
                $estadoTxt = SolicitudMaterial::$estados[$item->estado] ?? 'Desconocido';

                echo "<tr>
                        <td>".e($item->proyecto?->nombre_proyecto ?? 'N/A')."</td>
                        <td>".e($ubicacionCompleta)."</td>
                        <td>".e($item->usuario?->nombre_completo ?? 'N/A')."</td>
                        <td>".e($item->fecha_solicitud)."</td>
                        <td style='text-align:center;'>Entregados: {$entregados} / Pendientes: {$faltantes}</td>
                        <td>".e($estadoTxt)."</td>
                    </tr>";
            }
            echo "</tbody></table>";
        }, 200, $headers);
    }

    public function create( )
    {
        Gate::authorize('solicitud.create');
        try {
            $title = 'Crear Solicitud de Material';
            $proyectos = Proyecto::whereIn('id_estado', [1, 5, 3])
                ->when(!(Auth::user()->isAdmin || Auth::user()->isTecnico || Auth::user()->isAlmacenista), function ($query) {
                    $query->where(function ($q) {
                        $q->where('id_user', Auth::user()->id)->orWhere('id_user_obra_blanca', Auth::user()->id);
                    });
                })
                ->select([
                    'id',
                    DB::raw("
                        CONCAT(
                            nombre_proyecto,
                            ' -- Estado: ',
                            CASE id_estado
                                WHEN 1 THEN 'En Desarrollo'
                                WHEN 3 THEN 'Entregado'
                                WHEN 5 THEN 'Posventas'
                            END
                        ) as nombre_proyecto
                    ")
                ])
                ->get()
                ->toArray();

            $materiales = InventarioMaterial::where('activo', 1)
                ->when(Auth::user()->isColab, function ($q){
                    $q->where('tipo', 1);
                })->get()->toArray();

            if(Auth::user()->isContratista){
                $arr = [1,2];
            } else if(Auth::user()->isTecnico){
                $arr = [4,5];
            } else {
                $arr = [3];
            }

            $fasesDB = InventarioMaterial::whereIn('fase', $arr)
                ->get(['id', 'fase'])
                ->groupBy('fase');

            $fases = collect($arr)->map(function ($fase) use ($fasesDB) {
                return [
                    'id'         => $fase,
                    'name_fase'  => InventarioMaterial::$fases[$fase],
                    'materiales' => isset($fasesDB[$fase])
                        ? $fasesDB[$fase]
                            ->pluck('id')
                            ->map(fn($id) => (string) $id)
                            ->values()
                            ->toArray()
                        : [],
                ];
            })->toArray();
            return view('solicitud.create', compact('title', 'materiales', 'proyectos', 'fases'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    public function save(Request $request)
    {
        Gate::authorize('solicitud.save');
        $inicio = microtime(true);

        $validated = $request->validate([
            'id_proyecto' => [
                'required',
                'integer',
                Rule::exists('proyectos', 'id'),
            ],
            'id_user' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'materiales' => ['required', 'array', 'min:1'],
            'materiales.*.id_material' => [
                'required',
                'integer',
                Rule::exists('inventario_materiales', 'id'),
                function ($attribute, $value, $fail) {
                    $material = InventarioMaterial::find($value);
                    if (!$material || $material->activo != 1) {
                        $fail('El material seleccionado no está disponible.');
                    }
                }
            ],
            'materiales.*.cantidad' => [
                'required',
                'numeric',
                'min:0.1'
            ],
            'observacion' => ['nullable', 'string'],
            'cotizar' => ['integer', 'in:0,1']
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $solicitud = SolicitudMaterial::create([
                    'id_user'         => $validated['id_user'],
                    'id_proyecto'     => $validated['id_proyecto'],
                    'fecha_solicitud' => now(),
                    'estado'          => $validated['cotizar'] == 1 ? 4 : 1,
                    'observacion'     => $validated['observacion'] ?? null,
                ]);
                foreach ($validated['materiales'] as $material) {
                    if($validated['cotizar'] == 1){
                        Cotizacion::create([
                            'id_solicitud' => $solicitud->id,
                            'id_material'  => $material['id_material'],
                            'cantidad'     => $material['cantidad']
                        ]);
                    } else {
                        $itemMaterial = InventarioMaterial::find($material['id_material']);
                        SolicitudItems::create([
                            'id_solicitud' => $solicitud->id,
                            'id_material'  => $material['id_material'],
                            'cantidad'     => $material['cantidad'],
                            'cantidad_solicitada' => $material['cantidad'],
                            'estado'       => 1,
                            'aprobado'     => $itemMaterial->aprobar == 1 ? 0 : 1,
                        ]);
                    }
                }
            });

            $this->registrar(
                request: $request,
                servicio: 'SolicitudController@save',
                tipo: 'exito',
                statusCode: 200,
                inicio: $inicio
            );

            return response()->json([
                'status' => true,
                'message' => ($validated['cotizar'] == 1)? 'Cotizacion registrada correctamente' : 'Solicitud registrada correctamente',
            ], 200);
        } catch (\Throwable $e) {

            $this->registrar(
                request: $request,
                servicio: 'SolicitudController@save',
                tipo: 'error_inesperado',
                statusCode: 500,
                inicio: $inicio,
                error: [
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                ]
            );

            return response()->json([
                'status' => false,
                'message' => 'Error al guardar la solicitud',
            ], 500);
        }
    }

    public function listaSolicitud($id)
    {
        Gate::authorize('solicitud.listaSolicitud');

        $solicitud = SolicitudItems::Join('inventario_materiales', 'solicitud_items.id_material', '=', 'inventario_materiales.id')
            ->Join('solicitud_material', 'solicitud_material.id', '=', 'solicitud_items.id_solicitud')
            ->leftJoin(
                'inventario_solicituds',
                function ($join) {
                    $join->on('solicitud_items.id_solicitud', '=', 'inventario_solicituds.id_solicitud')
                        ->on('solicitud_items.id_material', '=', 'inventario_solicituds.id_material');
                }
            )
            ->leftJoin('users', 'solicitud_items.id_user_aprueba', '=', 'users.id')
            ->where('solicitud_items.id_solicitud', $id)
            ->select(
                'solicitud_material.id_proyecto',
                'inventario_materiales.nombre_material',
                'solicitud_items.cantidad_solicitada as cantidad_sol',
                'inventario_solicituds.cantidad as cantidad_des',
                'inventario_solicituds.cobro',
                'solicitud_items.estado',
                'solicitud_items.id_user_aprueba',
                'solicitud_items.aprobado',
                'inventario_solicituds.createdAt',
                'inventario_solicituds.codigo',
                'solicitud_material.fecha_solicitud',
                'users.nombre_completo as usuario_aprueba',
                'solicitud_items.fecha_aprobacion',
                'solicitud_items.nota_aprobacion'
                )
            ->get()
            ->map(function ($item) {
                $span = $item->span_estado;
                $isCobro = $item->cobro ? ($item->cobro == 1 ? '<span class="span-green">SI</span>':'<span class="span-red">NO</span>') : '';
                $item['span_estado'] = $span;
                $item['isCobro'] = $isCobro;
                return $item;
            });

            return response()->json([
                'status' => true,
                'data' => $solicitud,
                'ban' => Auth::user()->isAdmin || Auth::user()->isAnalista || Auth::user()->isColab
            ], 200);
    }

    public function createDespachoSolicitud($id){
        session(['solicitud_anterior_url' => url()->previous()]);
        return $this->createSolicitud($id, false);
    }

    public function createAprobarSolicitud($id){
        session(['solicitud_anterior_url' => url()->previous()]);
        return $this->createSolicitud($id, true);
    }

    public function createSolicitud($id, $isAnalista)
    {
        $solicitud = SolicitudMaterial::with('proyecto')->find($id);

        $solItemsArray = SolicitudItems::with(['material', 'despachado'])
            ->where('id_solicitud', $id)
            ->where(function ($query) use ($isAnalista) {
                if ($isAnalista) {
                    $query->where('aprobado', 0);
                } else {
                    $query->whereIn('estado', [1, 2])->where('aprobado', 1);
                }
            })
            ->get()
            ->map(function ($item) use ($isAnalista) {
                $cantidad_inventario = (int) $item->material->cantidad;
                $cantidad_solicitada = (int) $item->cantidad;
                $diff = $cantidad_inventario - $cantidad_solicitada;

                // Ajustes de lógica para valores negativos
                if($isAnalista) {
                    $pendiente = 0;
                } else {
                    if ($diff <= 0) {
                        $pendiente = abs($diff);
                    } else {
                        $pendiente = 0;
                    }
                }

                return [
                    'id_material' => $item->id_material,
                    'nombre_material' => $item->material->nombre_material,
                    'descripccion' => $item->material->descripccion,
                    'unidades' => $item->material->unidades,
                    'spanTipo' => $item->material->spanTipo,
                    'spanEstadoMate' => $item->material->spanEstado,
                    'valor_unidad' => (float) $item->valor_unidad,
                    'cantidad_inventario' => $cantidad_inventario,
                    'cantidad_solicitada' => $cantidad_solicitada - $pendiente,
                    'pendiente' => $pendiente,
                    'estado' => $item->estado,
                    'estadoSpan' => $item->estadoSpan,
                    'fase' => $item->material?->fase??'',
                ];
            })->toArray();

        $title = $isAnalista ? "Aprobar items para despacho" : "Despacho de Solicitud";

        return view('solicitud.createDespachoSolicitud', compact('title', 'solicitud', 'solItemsArray', 'isAnalista'));
    }

    public function saveSolicitud(SaveSolicitudRequest $request)
    {
        $validated = $request->validated();
        $inicio    = microtime(true);

        $this->registrar(
            request: $request,
            servicio: 'SolicitudController@saveSolicitud[aprobacion]',
            tipo: 'exito',
            statusCode: 200,
            inicio: $inicio
        );

        DB::beginTransaction();
        try {

            $arr = $validated['materiales'];
            $idSolicitud = $validated['id_solicitud'];

            //Aprobar items si es analista
            if($validated['isAnalista']){
                foreach ($arr as $item) {
                    $update = ['aprobado' => 1, 'fecha_aprobacion' => now(), 'id_user_aprueba' => Auth::user()->id, 'nota_aprobacion' => $item['nota_aprobacion'] ?? null];
                    if ($item['cancelo'] == 1) {
                        $update = array_merge($update, ['estado' => 4]); // Cancelado
                    } else {
                        $update = array_merge($update, ['cantidad' => $item['cantidad'], 'cantidad_solicitada' => $item['cantidad']]); // Aprobado
                    }

                    SolicitudItems::where([
                        'id_solicitud' => $idSolicitud,
                        'id_material'  => $item['id_material']
                    ])->update($update);
                }
                DB::commit();
                return redirect(session('solicitud_anterior_url', route('solicitud.index')))
                    ->with('success', "Aprobacion de items realizada con exito");
            }

            //Proceso de despacho
            $solMaterial = SolicitudMaterial::find($idSolicitud);
            $codigo = Despachos::generarCodigoUnico();//codigo de este despacho unico para este depacho
            $dato = [
                'id_solicitud' => $idSolicitud,
                'tipo' => 1,
                'codigo' => $codigo,
                'id_user' => $solMaterial->id_user,
                'id_user_despacho' => Auth::user()->id,
                'id_proyecto' => $solMaterial->id_proyecto,
            ];

            foreach ($arr as $item) {

                if (isset($item['cantidad']) && ($item['cantidad'] == 0) && $item['cancelo'] == 0) {
                    continue; // Salta al siguiente ciclo
                }

                if ($item['cancelo'] == 1) {
                    $estado = 4; // Cancelado
                } else {
                    $material = InventarioMaterial::find($item['id_material']);
                    if($material->activo != 1){
                        continue;
                    }
                    $solItem = SolicitudItems::where([
                        'id_solicitud' => $idSolicitud,
                        'id_material'  => $item['id_material']
                    ])->first();

                    $diff = $item['cantidad'] - $solItem->cantidad;// Diferencia entre solicitado y lo que ingresa el usuario

                    // Determinar estado
                    if ($diff == 0) {
                        $estado = 3; // Completo
                    } else {
                        $estado = 2; // Parcial
                    }
                }

                $upd = ['estado' => $estado];// Actualización del item

                if ($estado != 4) {
                    $material->decrement('cantidad', $item['cantidad']);// Descontar del inventario SOLO la cantidad despachada

                    $upd['cantidad'] = $solItem->cantidad - $item['cantidad'];// Actualizar cantidad pendiente

                    $dato['id_material'] = $item['id_material'];
                    $dato['cantidad'] = $item['cantidad'];
                    $dato['valor_unidad'] = $material['valor_unidad'];
                    $dato['cobro'] = $item['cobro'];
                    Despachos::create($dato);
                }

                SolicitudItems::where([
                    'id_solicitud' => $idSolicitud,
                    'id_material'  => $item['id_material']
                ])->update($upd);

            }

            // Actualizar estado de la solicitud
            $solItemCount = SolicitudItems::where('id_solicitud', $idSolicitud)
                ->whereIn('estado', [1, 2])
                ->count();

            $estado = $solItemCount == 0 ? 3 : 2;
            $solMaterial->update(['estado' => $estado]);

            //DB::commit();
            //return redirect()->route('solicitud.index')->with('success', "Despacho realizado con exito");
            DB::commit();

            $this->registrar(
                request: $request,
                servicio: 'SolicitudController@saveSolicitud[despacho]',
                tipo: 'exito',
                statusCode: 200,
                inicio: $inicio
            );

            return redirect(session('solicitud_anterior_url', route('solicitud.index')))
                ->with('success', 'Despacho realizado con éxito')
                ->with('despacho_codigo', $codigo)
                ->with('despacho_id_proyecto', $solMaterial->id_proyecto);
        } catch (\Illuminate\Database\QueryException $e) {
            $this->registrar(
                request: $request,
                servicio: 'SolicitudController@saveSolicitud',
                tipo: 'error_inesperado',
                statusCode: 500,
                inicio: $inicio,
                error: [
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                ]
            );
            DB::rollBack();
            return back()->with('error', 'Error en la base de datos: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->registrar(
                request: $request,
                servicio: 'SolicitudController@saveSolicitud',
                tipo: 'error_inesperado',
                statusCode: 500,
                inicio: $inicio,
                error: [
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                ]
            );
            DB::rollBack();
            return back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    public function delete($id){
        //Gate::authorize('otro_si.delete');
        $inicio  = microtime(true);
        $request = request();

        $item =  SolicitudMaterial::find($id);
        if($item && !($item->estado == 1 || $item->estado == 4)){
            return back()->with('error', 'Solo se pueden eliminar Solicitudes de Material en estado "Nuevo" o "Cotizacion".');
        }
        $item->items()->delete();
        $item->cotizacion()->delete();
        $item->delete();

        $this->registrar(
            request: $request,
            servicio: 'SolicitudController@delete',
            tipo: 'exito',
            statusCode: 200,
            inicio: $inicio
        );
        return redirect()->route('solicitud.index')->with('success', " Solicitudes de Material eliminada con exito");
    }

    public function solicitarCotizacion($id)
    {
        Gate::authorize('solicitud.solicitarCotizacion');
        $inicio  = microtime(true);
        $request = request();

        try {
            DB::transaction(function () use ($id) {
                $solicitud = SolicitudMaterial::with('cotizacion')->findOrFail($id);
                $solicitud->update(['estado' => 1]);
                foreach ($solicitud->cotizacion as $material) {
                    $itemMaterial = InventarioMaterial::find($material->id_material);
                    SolicitudItems::create([
                        'id_solicitud' => $solicitud->id,
                        'id_material'  => $material->id_material,
                        'cantidad'     => $material->cantidad,
                        'cantidad_solicitada' => $material->cantidad,
                        'estado'       => 1,
                        'aprobado'     => $itemMaterial && $itemMaterial->aprobar == 1 ? 0 : 1,
                    ]);
                }
            });

            $this->registrar(
                request: $request,
                servicio: 'SolicitudController@solicitarCotizacion',
                tipo: 'exito',
                statusCode: 200,
                inicio: $inicio
            );

            return response()->json([
                'success' => true,
                'message' => 'Cotización despachada'
            ]);
        } catch (\Throwable $e) {
            $this->registrar(
                request: $request,
                servicio: 'SolicitudController@solicitarCotizacion',
                tipo: 'error_inesperado',
                statusCode: 500,
                inicio: $inicio,
                error: [
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Error al despachar'
            ], 500);
        }
    }
}
