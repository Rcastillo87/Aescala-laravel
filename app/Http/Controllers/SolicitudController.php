<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\SolicitudMaterial;
use App\Models\InventarioMaterial;
use App\Models\Proyecto;
use App\Models\SolicitudItems;
use App\Models\User;
use App\Models\Despachos;
use App\Models\Fase;
use App\Http\Requests\SaveSolicitudRequest;

class SolicitudController extends Controller
{
    public function index( ) 
    {
        if(Auth::user()->isAdmin || Auth::user()->isAnalista){
            $cola = Request('id_userSerch');
        } else {
            $cola = Auth::user()->id;
        }

        $estadosItems = SolicitudItems::$estados;

        $title = 'Lista de Solicitud de Material';
        $items = SolicitudMaterial::with(['proyecto', 'usuario'])
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
            ->when(!(Auth::user()->isAdmin || Auth::user()->isAnalista), function ($query) {
                $query->where('id_user', Auth::User()->id);
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->appends(request()->query());

        $proyecto = Proyecto::with('tareas')
            ->when($cola, function ($query, $id_user) {
                $query->where('id_user', $id_user);
            })
            ->where('id_estado', [1, 5])
            ->get();

        $estados = SolicitudMaterial::$estados;
        $userColab = User::where('id_rol', 3)->where('activo', 1)->get(['id', 'nombre_completo'])->toArray();

        $headers = ['Nombre del Proyecto', 'Quien Solicito', 'Fecha de solicitud', 'Entregados y Faltantes', 'Estado Solicitud', 'Estado Items', 'Observacion', 'Opciones'];
        if(!(Auth::User()->isAdmin || Auth::user()->isAnalista)) {
            $headers = ['Nombre del Proyecto', 'Fecha de solicitud', 'Entregados y Faltantes', 'Estado', 'Observacion', 'Opciones'];
        }
        return view('solicitud.index', compact('title', 'items', 'headers', 'proyecto', 'estados', 'estadosItems', 'userColab'));
    }

    public function create( )
    {
        $title = 'Crear Solicitud de Material';
        $proyectos = Proyecto::wherein('id_estado', [1, 5])
            ->when(!(Auth::user()->isAdmin || Auth::user()->isTecnico || Auth::user()->isAlmacenista), function ($query) {
                $query->where('id_user', Auth::user()->id)
                    ->orwhere('id_user_obra_blanca', Auth::user()->id)
                    ->orwhere('id_user_carpinteria', Auth::user()->id);
            })
            ->get(['id', 'id_user', 'nombre_proyecto'])
            ->toArray();
        
        $materiales = InventarioMaterial::when(
            Auth::user()->isAlmacenista, fn ($q) => $q->where('tipo', 1)
        )->where('activo', 1)->get()->toArray();

        if(Auth::user()->isContratista){
            $arr = [1,2];
        } else if(Auth::user()->isTecnico){
            $arr = [4,5];
        } else {
            $arr = [3];
        }
        $fases = Fase::whereIn('id', $arr)->get()->map(function ($item) {
            $ids = explode(',', $item['id_materiales']);
            return [ 
                'id' => $item['id'],
                'name_fase' => $item['fase'],
                'materiales' => $ids
            ];
        })->toArray();

        return view('solicitud.create', compact('title', 'materiales', 'proyectos', 'fases'));
    }

    public function save(Request $request)
    {
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
                'integer',
                'min:1'
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
            });
            return response()->json([
                'status' => true,
                'message' => 'Solicitud registrada correctamente',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al guardar la solicitud',
            ], 500);
        }
    }

    public function listaSolicitud($id)
    {
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
        return $this->createSolicitud($id, false);
    }

    public function createAprobarSolicitud($id){
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
                    'estadoSpan' => $item->estadoSpan
                ];
            })->toArray();

        $title = $isAnalista ? "Aprobar items para despacho" : "Despacho de Solicitud";

        return view('solicitud.createDespachoSolicitud', compact('title', 'solicitud', 'solItemsArray', 'isAnalista'));
    }

    public function saveSolicitud(SaveSolicitudRequest $request)
    {
        $validated = $request->validated();

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
                return redirect()->route('solicitud.index')->with('success', "Aprobacion de items realizada con exito");
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

            DB::commit();
            return redirect()->route('solicitud.index')->with('success', "Despacho realizado con exito");
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return back()->with('error', 'Error en la base de datos: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    public function delete($id){
        $item =  SolicitudMaterial::find($id);
        if($item && ($item->estado != 1)){
            return back()->with('error', 'Solo se pueden eliminar Solicitudes de Material en estado "Nuevo".');
        }
        $item->items()->delete();
        $item->delete();
        return redirect()->route('solicitud.index')->with('success', " Solicitudes de Material eliminada con exito");
    }
}
