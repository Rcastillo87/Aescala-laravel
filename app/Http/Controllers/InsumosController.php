<?php

namespace App\Http\Controllers;

use App\Models\AreasEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Gate;

use App\Models\Insumos;
use App\Models\InsumoEntregado;

class InsumosController extends Controller
{

    public function index()
    {
        Gate::authorize('insumos.index');
        $title  = 'Lista de Insumos';
        $perPage = request('per_page', 10);

        $items = Insumos::when(request('nombre_insumo'), fn ($q, $v) =>
                $q->whereRaw('LOWER(nombre_insumo) LIKE LOWER(?)', ["%{$v}%"])
            )
            ->when(request('codigo'), fn ($q, $v) =>
                $q->whereRaw('LOWER(codigo) LIKE LOWER(?)', ["%{$v}%"])
            )
            ->when(request('estado'), fn ($q, $v) =>
                $q->where('estado', $v)
            )
            ->when(request('rango'), function ($q, $rango) {
                match ((int) $rango) {
                    1 => $q->where('cantidad', 0)->where('cantidad_min', '<>', 0),
                    2 => $q->where(function ($x) {
                            $x->whereColumn('cantidad', '<=', 'cantidad_min')->where('cantidad', '>', 0);
                        })->orWhere(function ($x) {
                            $x->where('cantidad', 0)->where('cantidad_min', 0);
                        }),
                    3 => $q->whereColumn('cantidad', '>', 'cantidad_min'),
                    default => null,
                };
            })
            ->paginate($perPage)
            ->withQueryString();

        $estados = Insumos::$estado;
        $areasEmpresa = AreasEmpresa::all()->toArray();
        $insumos = Insumos::where('estado', 1)
        ->select(
            'id',
            DB::raw("CONCAT(
                    nombre_insumo,
                    ' -- Hay en inventario: ',
                    cantidad
                ) as nombre_insumo"),
            'cantidad'
            )->get()->toArray();

        /** columnas del componente */
        $columns = [
            'nombre_insumo',
            'codigo',
            'estado',
            'cantidad',
            'cantidad_min',
            'descripccion',
            'acciones',
        ];

        /** headers con diseño */
        $headers = [
            'nombre_insumo'     => 'Nombre Insumo',
            'codigo'            => 'Codigo',
            'estado'            => 'Estado',
            'cantidad'          => 'Cantidad',
            'cantidad_min'      => 'Cantidad Minima',
            'descripccion'      => 'Descripccion',
            'acciones'          => 'Acciones'
        ];

        $headerModal = [
            'Insumo Entregado',
            'Area Entregada',
            'Cantidad',
            'Fecha Entrega'
        ];

        return view('insumos.index', compact(
            'title',
            'items',
            'columns',
            'headers',
            'estados',
            'areasEmpresa',
            'insumos',
            'headerModal'
        ));
    }

    public function create( )
    {
        Gate::authorize('insumos.create');
        session(['solicitud_anterior_url' => url()->previous()]);
        return $this->form();
    }

    public function edit($id)
    {
        Gate::authorize('insumos.edit');
        session(['solicitud_anterior_url' => url()->previous()]);
        return $this->form($id);
    }

    public function form($id = null)
    {
        $item = $id ? Insumos::find($id) : null;
        $title = $id ? 'Editar Insumo' : 'Crear Insumo';
        $action = $id ? 'Editar' : 'Crear';
        $estados = Insumos::$estado;
        return view('insumos.create', compact('title', 'action', 'estados', 'item'));
    }

    public function save(Request $req)
    {
        Gate::authorize('insumos.save');
        $data = $req->validate([
            'id' => 'nullable|integer',
            'nombre_insumo' => [
                'required',
                'string',
                'max:100',
                Rule::unique('insumos', 'nombre_insumo')->ignore($req->id)
            ],

            'codigo' => [
                'required',
                'string',
                'max:20',
                Rule::unique('insumos', 'codigo')->ignore($req->id)
            ],

            'cantidad' => ['required', 'integer', 'min:0'],
            'cantidad_min' => ['required', 'integer', 'min:0'],
            'descripccion' => 'nullable'
        ],
        [
            // 🔴 MENSAJES PERSONALIZADOS
            'nombre_insumo.unique' => 'Este nombre de insumo ya existe.',
            'codigo.unique' => 'Este código ya está registrado.',
        ],
        [
            // 🟢 ATTRIBUTES (nombres bonitos)
            'nombre_insumo' => 'nombre del insumo',
            'codigo' => 'código',
            'cantidad' => 'cantidad',
            'cantidad_min' => 'cantidad mínima',
            'descripccion' => 'descripción'
        ]);

        if (!$req->id) {
            $msg = 'Insumo creado con éxito';
        } else {
            $msg = 'Insumo editado con éxito';
        }

        try {
            DB::beginTransaction();
            Insumos::updateOrCreate(
                ['id' => $req->id],
                $data
            );
            DB::commit();
            return redirect(session('solicitud_anterior_url', route('material.index')))->with('success', $msg);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return back()->with('error', 'Error en la base de datos: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    public function editStatus($id)
    {
        Gate::authorize('insumos.editStatus');
        try {
            DB::beginTransaction();
            $user = Insumos::findOrFail($id);
            $user->update(['estado' => ($user->estado == 1) ? 2 : 1]);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Usuario actualizado correctamente.'
            ],200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function entregaInsumo(Request $req)
    {
        Gate::authorize('insumos.entregaInsumo');
        try {
            $data = $req->validate([
                'id_insumo' => ['required', 'integer', 'exists:insumos,id'],
                'cantidad' => ['required', 'integer', 'min:1'],
                'id_area_empresa' => ['required', 'integer', 'exists:areas_empresa,id'],
            ], [], [
                'id_insumo' => 'insumo',
                'cantidad' => 'cantidad',
                'id_area_empresa' => 'área'
            ]);

            DB::beginTransaction();

            // 🔒 BLOQUEO
            $insumo = Insumos::lockForUpdate()->find($data['id_insumo']);

            if (!$insumo) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Insumo no encontrado'
                ], 404);
            }

            // 🔴 VALIDACIÓN REAL
            if ($data['cantidad'] > $insumo->cantidad) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'errors' => [
                        'cantidad' => ['La cantidad supera el stock disponible (' . $insumo->cantidad . ')']
                    ]
                ], 422);
            }

            // 🔹 DESCONTAR STOCK
            $insumo->cantidad -= $data['cantidad'];
            $insumo->save();

            // 🔹 GUARDAR ENTREGA
            InsumoEntregado::create([
                'id_area_empresa' => $data['id_area_empresa'],
                'id_user' => null,
                'id_insumo' => $data['id_insumo'],
                'cantidad' => $data['cantidad'],
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Entrega registrada correctamente'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error interno'
            ], 500);
        }
    }

    public function history()
    {
        Gate::authorize('insumos.history');
        $title  = 'Lista Historial de Entregas';
        $perPage = request('per_page', 10);

        $items = InsumoEntregado::with(['insumo', 'area_empresa'])
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        /** columnas del componente */
        $columns = [
            'nombre_insumo',
            'area_empresa',
            'cantidad',
            'created_at'
        ];

        /** headers con diseño */
        $headers = [
            'nombre_insumo'     => 'Insumo Entregado',
            'area_empresa'      => 'Area Entregada',
            'cantidad'          => 'Cantidad',
            'created_at'        => 'Fecha Entrega'
        ];

        return view('insumos.history', compact(
            'title',
            'items',
            'columns',
            'headers',
        ));
    }

    public function historyInsumo()
    {
        Gate::authorize('insumos.historyInsumo');
        $items = InsumoEntregado::with(['insumo', 'area_empresa'])
            ->where('id_insumo', Request('id'))
            ->orderBy('id', 'desc')
            ->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $items,
            'message' => 'Lista Generada Correctamente'
        ], 200);
    }

}
