<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

use App\Models\Insumos;
use App\Models\User;

class InsumosController extends Controller
{

    public function index()
    {
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

        return view('insumos.index', compact(
            'title',
            'items',
            'columns',
            'headers',
            'estados'
        ));
    }

    public function create( )
    {
        session(['solicitud_anterior_url' => url()->previous()]);
        return $this->form();
    }

    public function edit($id)
    {
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
}
