<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

use App\Models\User;
use App\Models\Despachos;
use App\Models\Proyecto;
use App\Models\InventarioMaterial;
use Illuminate\Support\Facades\Auth;

class DespachoController extends Controller
{

    public function index( )
    {
        Gate::authorize('despachos.index');
        $title = 'Despacho de Material';
        $tipo = Despachos::$tipo;
        $colaUsers = User::whereIn('id_rol', [3, 7, 8]) // Tecnico y Contratista y architecto
            ->where('activo', 1)
            ->get(['id', 'nombre_completo'])
            ->toArray();

        $proyectos = Proyecto::wherein('id_estado', [1, 5])
            ->get(['id', 'id_user', 'nombre_proyecto'])
            ->toArray();

        $materiales = InventarioMaterial::where('activo', 1)->get()->toArray();

        return view('despachos.index', compact('title', 'tipo', 'colaUsers', 'proyectos', 'materiales'  ));
    }

    public function save(Request $request)
    {
        Gate::authorize('despachos.save');
        $validated = $request->validate([
            'id_proyecto' => ['required','integer', Rule::exists('proyectos', 'id')],
            'id_user' => ['required','integer', Rule::exists('users', 'id')],
            'tipo' => ['required','integer', Rule::in(array_keys(Despachos::$tipo))],

            'materiales' => ['required','array','min:1'],
            'materiales.*.id_material' => [
                'required','integer',
                Rule::exists('inventario_materiales', 'id'),
                function ($attribute, $value, $fail) {
                    $material = InventarioMaterial::find($value);
                    if (!$material || $material->activo != 1) {
                        $fail('El material seleccionado no está disponible.');
                    }
                }
            ],
            'materiales.*.cantidad' => [
                'required','integer','min:1',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $materialId = $request->input("materiales.$index.id_material");
                    $material = InventarioMaterial::find($materialId);

                    if ($material && $request->tipo != 2 && $value > $material->cantidad) {
                        $fail("La cantidad para {$material->nombre_material} excede el stock ({$material->cantidad}).");
                    }
                }
            ],
            'materiales.*.valor_unidad' => ['required','integer'],
            'materiales.*.valor_inventario' => ['required','integer'],
            'materiales.*.cobro' => ['required','integer','in:0,1'],
            'materiales.*.id_ref_devolucion' => ['nullable','integer', Rule::exists('inventario_solicituds', 'id')],
        ]);

        try {
            $result = DB::transaction(function () use ($validated) {

                $codigo = Despachos::generarCodigoUnico();

                foreach ($validated['materiales'] as $material) {

                    Despachos::create([
                        'tipo'             => $validated['tipo'],
                        'codigo'           => $codigo,
                        'id_user'          => $validated['id_user'],
                        'id_user_despacho' => Auth::user()->id,
                        'id_proyecto'      => $validated['id_proyecto'],
                        'id_material'      => $material['id_material'],
                        'cantidad'         => $material['cantidad'],
                        'valor_unidad'     => $material['valor_unidad'],
                        'valor_inventario' => $material['valor_inventario'],
                        'cobro'            => $material['cobro'],
                        'id_ref_devolucion' => $material['id_ref_devolucion'] ?? null,
                    ]);

                    $inventario = InventarioMaterial::find($material['id_material']);

                    if ($validated['tipo'] == 2) {
                        $inventario->increment('cantidad', $material['cantidad']);
                        $accion = 'Devolución';
                    } else {
                        $inventario->decrement('cantidad', $material['cantidad']);
                        $accion = 'Despacho';
                    }
                }

                return [
                    'codigo' => $codigo,
                    'accion' => $accion,
                    'pdf_url' => route('proyecto.pdfDespacho', [
                        'id' => $validated['id_proyecto'],
                        'codigo' => $codigo,
                        'view' => 1
                    ])
                ];
            });

            return response()->json([
                'success' => true,
                'message' => "{$result['accion']} {$result['codigo']} registrado correctamente",
                'data' => $result
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el despacho',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function historialMateriales(Request $req)
    {
        Gate::authorize('despachos.historialMateriales');
        $despachos = Despachos::with(['material'])
            ->where('id_proyecto', $req->input('id_proyecto'))
            ->where('tipo', 1)
            ->select('*', DB::raw("DATE(createdAt) as fecha"))
            ->orderBy('createdAt', 'desc')
            ->get()

            // agrupamos por codigo + fecha
            ->groupBy(function ($item) {
                return $item->codigo . '_' . $item->fecha;
            })

            ->map(function ($group) {

                $first = $group->first();

                return [
                    'codigo'    => $first->codigo,
                    'createdAt' => $first->fecha,
                    'items' => $group->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'id_material' => $item->id_material,
                            'cantidad' => $item->cantidad,
                            'valor_unidad' => $item->valor_unidad,
                            'valor_inventario' => $item->cantidad * $item->valor_unidad,
                            'cobro' => $item->cobro,
                            'material_nombre' => optional($item->material)->nombre_material ?? 'N/A',
                            'spanTipo' => optional($item->material)->spanTipo ?? 'N/A',
                            'unidades'        => $item->material->unidades,
                        ];
                    })->values()

                ];
            })
            ->values();
        return response()->json(['status' => 'true', 'results' => $despachos], 200);
    }

    public function indexDespachos(Request $request)
    {
        Gate::authorize('despachos.indexDespachos');
        $title = 'Historial Despachos de Materiales';
        $perPage = request('per_page', 10);
        $colaUsers = User::whereIn('id_rol', [3, 7, 8])
            ->where('activo', 1)
            ->get(['id', 'nombre_completo'])
            ->toArray();

        $materiales = InventarioMaterial::where('activo', 1)->get(['id', 'nombre_material'])->toArray();

        $query = Despachos::query()
            ->join('proyectos', 'proyectos.id', '=', 'inventario_solicituds.id_proyecto')
            ->join('users', 'users.id', '=', 'inventario_solicituds.id_user')
            ->join('inventario_materiales', 'inventario_materiales.id', '=', 'inventario_solicituds.id_material')

            ->when(request('nombre_proyecto'), function ($q) {
                $q->where('proyectos.nombre_proyecto', 'like', '%' . request('nombre_proyecto') . '%');
            })

            ->when(request('id_userSerch'), function ($q) {
                $q->where('inventario_solicituds.id_user', request('id_userSerch'));
            })

            ->when(request('id_material'), function ($q) {
                $q->where('inventario_materiales.id', request('id_material'));
            });

        $items = $query
            ->select('inventario_solicituds.codigo')
            ->groupBy('inventario_solicituds.codigo')
            ->orderByRaw('MAX(inventario_solicituds.createdAt) DESC')
            ->paginate($perPage)
            ->appends(request()->query());


        $agrupados = Despachos::with(['proyecto', 'user', 'material', 'user_despacho'])
            ->whereIn('codigo', $items->pluck('codigo'))
            ->orderBy('createdAt', 'desc')
            ->get()
            ->groupBy('codigo')
            ->map(function ($group) {

                $first = $group->first();

                return (object)[
                    'codigo' => $first->codigo,
                    'id_proyecto' => $first->id_proyecto,
                    'tipo' =>  $first->spanEstado,
                    'proyecto' => optional($first->proyecto)->nombre_proyecto ?? 'N/A',
                    'usuario' => optional($first->user)->nombre_completo ?? 'N/A',
                    'usuarioDespacho' => optional($first->user_despacho)->nombre_completo ?? 'N/A',
                    'cantidad_items' => $group->count(),
                    'total_cobro' => '$ ' . number_format(
                        $group->where('cobro', 1)
                            ->sum(fn($item) => $item->cantidad * $item->valor_unidad), 2
                    ),
                    'createdAt' => $group->max('createdAt'),
                ];
            });

        $items->getCollection()->transform(function ($item) use ($agrupados) {
            return $agrupados[$item->codigo];
        });

        $columns = [
            'codigo',
            'tipo',
            'proyecto',
            'usuario',
            'usuarioDespacho',
            'cantidad_items',
            'total_cobro',
            'createdAt',
            'acciones',
        ];

        /** headers con diseño */
        $headers = [
            'codigo'     => 'Código',
            'tipo'       => 'Tipo',
            'proyecto'   => 'Nombre Proyecto',
            'usuario'    => 'Usuario que recibe el despacho',
            'usuarioDespacho'    => 'Usuario que Despacho',
            'cantidad_items' => 'Cantidad de Items',
            'total_cobro'     => 'Total a Cobrar',
            'createdAt'  => 'Fecha de Registro',
            'acciones'   => 'Opciones',
        ];

        return view('despachos.indexDespachos', compact('title', 'items', 'colaUsers', 'columns', 'headers', 'materiales'));
    }
}
