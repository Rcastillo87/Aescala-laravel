<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\InventarioMaterial;
use App\Models\Proveedor;
use App\Models\Almacenes;


class MaterialController extends Controller
{

    public function index()
    {
        $title = 'Lista de Materiales';
        $estado = InventarioMaterial::$estado;
        $tipos  = InventarioMaterial::$tipo;
        $perPage = request('per_page', 10);

        if(Auth::user()->isAlmacenista){
            $tipo = Almacenes::where('id_user', Auth::user()->id)->first()->tipo;
        } else if(Auth::user()->isAdmin || Auth::user()->isUser){
            $tipo  = request('tipo');
        } else {
            return back()->with('error', 'No posees el perfil para entrar en este  modulo.');
        }

        $query = InventarioMaterial::query()
            ->when(request('nombre_material'), function ($q, $nombre) {
                foreach (preg_split('/\s+/', trim($nombre)) as $palabra) {
                    $q->whereRaw('LOWER(nombre_material) LIKE ?', ['%' . strtolower($palabra) . '%']);
                }
            })
            ->when(request('codigo'), fn ($q, $codigo) =>
                $q->whereRaw('LOWER(codigo) LIKE LOWER(?)', ["%$codigo%"])
            )
            ->when(request()->filled('aprobar'),
                fn ($q) => $q->where('aprobar', request('aprobar'))
            )
            ->when($tipo,
                fn ($q, $tipo) => $q->where('tipo', $tipo)
            )
            ->when(request('zona'), fn ($q, $zona) =>
                $q->where('zona', $zona)
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

            ->when(request('estado'),
                fn ($q, $estado) => $q->where('activo', $estado)
            )

            ->when(request('id_proveedor'),
                fn ($q, $id) => $q->where('id_proveedor', $id)
            );

        if (request('export') == 1) {
            return $this->exportExcel($query->get());
        }

        $items = $query->paginate($perPage)->appends(request()->query());

        $zonas = InventarioMaterial::$zonas;

        $columns = [
            'nombre_material',
            'codigo',
            'stock',
            'valores',
            'proveedor',
            'tipo',
            'estado',
            'zona',
            'acciones',
        ];

        $headers = [
            'nombre_material' => 'Nombre Material',
            'codigo'          => 'Código',
            'stock'           => 'Stock / Stock Min',
            'valores'         => 'Valor Venta / Inventario',
            'proveedor'       => 'Proveedor Principal',
            'tipo'            => 'Tipo Material',
            'estado'          => 'Estado / Requiere Aprobación',
            'zona'           => 'Zonas',
            'acciones'        => 'Opciones',
        ];

        $proveedores = Proveedor::where('activo', 1)->get(['id', 'razon_social'])->toArray();

        return view('material.index', compact(
            'title',
            'items',
            'columns',
            'headers',
            'estado',
            'tipos',
            'proveedores',
            'zonas'
        ));
    }

    private function exportExcel($items)
    {
        $headers = [
            "Content-Type" => "application/vnd.ms-excel; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=inventario_materiales.xls"
        ];

        return response()->stream(function () use ($items) {

            // 🔑 BOM UTF-8 (corrige acentos y evita corrupción)
            echo "\xEF\xBB\xBF";

            echo "<table border='1' style='border-collapse:collapse'>
                <thead>
                    <tr style='background:#242e68;color:#fff;font-weight:bold'>
                        <th>Nombre Material</th>
                        <th>Código</th>
                        <th>Tipo Material</th>
                        <th>Cantidad</th>
                        <th>Cantidad Mínima</th>
                        <th>Unidad de medida</th>
                        <th>Valor Venta</th>
                        <th>Valor Inventario</th>
                        <th>Proveedor Principal</th>
                        <th>Estado Item</th>
                        <th>Requiere Aprobación</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>";

            foreach ($items as $item) {
                echo "<tr>
                    <td>".e($item->nombre_material)."</td>
                    <td style=\"mso-number-format:'\\@'\">".e($item->codigo)."</td>
                    <td>".e($item->tipo_material)."</td>
                    <td>{$item->cantidad}</td>
                    <td>{$item->cantidad_min}</td>
                    <td>".e($item->unidades)."</td>
                    <td>{$item->valor_unidad}</td>
                    <td>{$item->valor_inventario}</td>
                    <td>".e(optional($item->proveedor)->razon_social)."</td>
                    <td>".e($item->estado)."</td>
                    <td>".($item->aprobar ? 'SI' : 'NO')."</td>
                    <td>".e($item->descripccion)."</td>
                </tr>";
            }

            echo "</tbody></table>";

        }, 200, $headers);
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
        $material = $id?InventarioMaterial::find($id):null;
        $title = $id?'Editar Materiales':'Crear Materiales';
        $unidades = InventarioMaterial::$unidades;
        $tipos = InventarioMaterial::$tipo;
        $proveedores = Proveedor::where('activo', 1)->get()->toArray();
        $zonas = InventarioMaterial::$zonas;

        if(Auth::user()->isAlmacenista){
            $tipo = Almacenes::where('id_user', Auth::user()->id)->first()->tipo;
        } else if(Auth::user()->isAdmin || Auth::user()->isUser){
            $tipo  = '';
        } else {
            return back()->with('error', 'No posees el perfil para entrar en este  modulo.');
        }
        return view('material.create', compact( 'title', 'unidades', 'tipos', 'material', 'proveedores', 'zonas', 'tipo'));
    }

    public function save(Request $req)
    {
        $data = $req->validate([
            'id' => 'nullable|integer',
            'codigo' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[A-Z0-9_-]+$/i',
                Rule::unique('inventario_materiales', 'codigo')
                    ->ignore($req->id, 'id'),
            ],
            'cantidad' => 'required|numeric|min:0',
            'cantidad_min' => 'required|numeric|min:0',
            'id_unidad' => Rule::in(array_keys(InventarioMaterial::$unidades)),
            'valor_unidad' => 'required|numeric|min:0',
            'valor_inventario' => 'required|numeric|min:0',
            'tipo' => ['nullable', Rule::in(array_keys(InventarioMaterial::$tipo))],
            'descripccion' => 'nullable|string',
            'nombre_material' => 'required|string',
            'aprobar' => ['required', 'integer', 'in:0,1'],
            'id_proveedor' => [
                'nullable',
                'integer',
                Rule::exists('inventario_proveedores', 'id')
            ],
            'zona' => [ 'nullable',  Rule::in(array_keys(InventarioMaterial::$zonas))],
        ]);

        if(!(Auth::user()->isAlmacenista || Auth::user()->isAdmin) && $req->id) {
            $data = array_diff_key($data, ['cantidad' => '']);
        }

        if (!$req->id) {
            $msg = 'Material creado con éxito';
        } else {
            $msg = 'Material editado con éxito';
        }

        try {
            DB::beginTransaction();
            InventarioMaterial::updateOrCreate(
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
            $user = InventarioMaterial::findOrFail($id);
            $user->update(['activo' => ($user->activo == 1) ? 2 : 1]);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Material actualizado correctamente.'
            ],200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el aterial: ' . $e->getMessage()
            ], 500);
        }
    }
}
