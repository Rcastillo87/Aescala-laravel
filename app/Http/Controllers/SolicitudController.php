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

class SolicitudController extends Controller
{
    public function index( ) 
    {
        if(Auth::user()->isAdmin){
            $cola = Request('id_userSerch');
        } else {
            $cola = Auth::user()->id;
        }

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
            ->when(!Auth::User()->isAdmin, function ($query) {
                $query->where('id_user', Auth::User()->id);
            })
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
        
        $headers = ['Nombre del Proyecto', 'Quien Solicito', 'Fecha de solicitud', 'Items Entregados', 'Estado', 'Opciones'];
        if(!Auth::User()->isAdmin) {
            $headers = ['Nombre del Proyecto', 'Fecha de solicitud', 'Items Entregados', 'Estado', 'Opciones'];
        }
        return view('solicitud.index', compact('title', 'items', 'headers', 'proyecto', 'estados', 'userColab'));
    }

    public function create( )
    {
        $title = 'Crear Solicitud de Material';
        $proyectos = Proyecto::wherein('id_estado', [1, 5])
            ->when(!Auth::user()->isAdmin, function ($query) {
                $query->where('id_user', Auth::user()->id);
            })
            ->get(['id', 'id_user', 'nombre_proyecto'])
            ->toArray();
	    $materiales = InventarioMaterial::where('activo', 1)->get()->toArray();
        return view('solicitud.create', compact('title', 'materiales', 'proyectos'));
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
        ]);

        return DB::transaction(function () use ($validated) {

            $solicitud = SolicitudMaterial::create([
                'id_user'         => $validated['id_user'],
                'id_proyecto'     => $validated['id_proyecto'],
                'fecha_solicitud' => now(),
                'estado'          => 1,
                'observacion'     => $validated['observacion'] ?? null,
            ]);

            foreach ($validated['materiales'] as $material) {
                SolicitudItems::create([
                    'id_solicitud' => $solicitud->id,
                    'id_material'  => $material['id_material'],
                    'cantidad'     => $material['cantidad'],
                    'estado'       => 1
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Solicitud registrada correctamente',
            ], 200);
        });
    }

    public function listaSolicitud($id)
    {
        $estado = SolicitudItems::$estados;
        $solicitud = SolicitudItems::Join('inventario_materiales', 'solicitud_items.id_material', '=', 'inventario_materiales.id')
            ->leftJoin('inventario_solicituds', 'solicitud_items.id_solicitud', '=', 'inventario_solicituds.id_solicitud')
            ->where('solicitud_items.id_solicitud', $id)
            ->select(
                'inventario_materiales.nombre_material', 
                'solicitud_items.cantidad as cantidad_sol', 
                'inventario_solicituds.cantidad as cantidad_des', 
                'solicitud_items.estado', 
                'inventario_solicituds.createdAt',
                'inventario_solicituds.codigo'
                )
            ->get()
            ->map(function ($item) {
                $span = $estado[$item->estado] ?? 'Desconocido';
                $item['span_estado'] = $span;
                return $item;
            });
            /*
            $solicitud = SolicitudItems::with([
                'material',
                'despachado'
            ])
            ->where('id_solicitud', $id)
            ->get()
            ->map(function ($item) {
                return [
                    'material'      => $item->material->nombre_material,
                    'cantidad_sol'  => $item->cantidad??'',
                    'cantidad_des'  => $item->despachado?->cantidad??'',//sum('cantidad'),
                    'spanEstado'    => $item->spanEstado,
                    'cod_despacho'  => $item->despachado?->codigo??'',
                    'fecha_despacho' => $item->despachado?->createdAt??''//optional($item->despachos->last())->createdAt
                ];
            });
            */

            return response()->json([
                'status' => true,
                'data' => $solicitud,
            ], 200);
    }

}