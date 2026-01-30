<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\InventarioMaterial;
use App\Models\Proveedor;


class MaterialController extends Controller
{

    public function index( ) 
    {
        $title = 'Lista de Materiales';
        $estado = InventarioMaterial::$estado;
        $tipos = InventarioMaterial::$tipo;
        $items = InventarioMaterial::when(Request('nombre_material'), function ($query, $nombre_material) { 
            $palabras = preg_split('/\s+/', trim($nombre_material));
            foreach ($palabras as $palabra) {
                $query->whereRaw(
                    'LOWER(nombre_material) LIKE ?',
                    ['%' . strtolower($palabra) . '%']
                );
            }
        })
        ->when(Request('codigo'), function ($query, $codigo) { 
            return $query->whereRaw('LOWER(codigo) LIKE LOWER(?)', ["%$codigo%"]);
        })
        ->when(request()->filled('aprobar'), function ($query) {
            return $query->where('aprobar', request('aprobar'));
        })
        ->when(Request('tipo'), function ($query, $tipo) { 
            return $query->where('tipo', $tipo);
        })
        ->when(Request('rango'), function ($query, $rango) { 
            if($rango == 1) {
                return $query->where('cantidad', '=', 0)->where('cantidad_min', '<>', 0);
            } elseif($rango == 2) {
                return $query->where(function($q){
                    $q->where('cantidad', '>', 'cantidad_min')->where('cantidad', '<=', DB::raw('cantidad_min'));
                })->orwhere(function($q){
                    $q->where('cantidad', 0)->where('cantidad_min', 0);
                });
            } elseif($rango == 3) {
                return $query->where('cantidad', '>', DB::raw('cantidad_min'));
            }
        })
        ->when(Request('estado'), function ($query, $estado) { 
            return $query->where('activo', $estado);
        })
        ->when(Request('id_proveedor'), function ($query, $id_proveedor) { 
            return $query->where('id_proveedor', $id_proveedor);
        });
        
        if (request('export') == 1) {
            return $this->exportExcel($items->get());
        }

        $items = $items->paginate(10)->appends(request()->query());

        $proveedores = Proveedor::where('activo', 1)->get()->toArray();
        $headers = ['Nombre Material', 'Codigo Material', 'Stock / Stock Min', 'Valor venta / Valor inventario', 'Proveedor Principal', 'Tipo Material', 'Estado / Requiere Aprobacion', 'Opciones'];
        return view('material.index', compact( 'title', 'items', 'headers', 'estado','tipos', 'proveedores'));
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
        return $this->form();
    }

    public function edit($id) 
    {
        return $this->form($id);
    }

    public function form($id = null)
    {
        $material = $id?InventarioMaterial::find($id):null;
        $title = $id?'Editar Materiales':'Crear Materiales';
        $unidades = InventarioMaterial::$unidades;
        $tipos = InventarioMaterial::$tipo;
        $proveedores = Proveedor::where('activo', 1)->get()->toArray();
        return view('material.create', compact( 'title', 'unidades', 'tipos', 'material', 'proveedores'));
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
            'tipo' => Rule::in(array_keys(InventarioMaterial::$tipo)),
            'descripccion' => 'nullable|string',
            'nombre_material' => 'required|string',
            'aprobar' => ['required', 'integer', 'in:0,1'],
            'id_proveedor' => [
                'nullable',
                'integer',
                Rule::exists('inventario_proveedores', 'id')
            ],
        ]);
        
        if((Auth::user()->id_rol == 2) && ($data['id'])) {
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
            return redirect()->route('material.index')->with('success', $msg);
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