<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

use App\Models\Pedidos;
use App\Models\InventarioMaterial;
use App\Models\Proyecto;
use App\Models\Proveedor;
use App\Models\Despachos;
use App\Models\Insumos;

use Illuminate\Support\Facades\Auth;

class PedidosController extends Controller
{

    public function index( )
    {
        Gate::authorize('pedidos.index');
        $title = 'Lista de Compras';
        $items = Pedidos::with(['proveedor', 'material'])
        ->selectRaw('id_factura,
                    DATE(fecha) as fecha,
                    MAX(codigo) as codigo,
                    id_proveedor,
                    SUM(cantidad * vr_unidad) as total,
                    COUNT(*) as items')
        ->when(request('factura'), function ($query, $factura) {
            return $query->where('id_factura', 'like', "%{$factura}%");
        })
        ->when(request('fecha'), function ($query, $fecha) {
            return $query->whereDate('fecha', '>=', $fecha);
        })
        ->when(request('fecha'), function ($query, $fecha) {
            return $query->whereDate('fecha', '<=', $fecha);
        })
        ->when(request('proveedor'), function ($query, $proveedor) {
            return $query->whereHas('proveedor', function ($q) use ($proveedor) {
                $q->where('razon_social', 'like', "%{$proveedor}%");
            });
        })
        ->groupBy('id_factura', 'id_proveedor', DB::raw('DATE(fecha)'))
        ->orderBy('fecha', 'desc')
        ->paginate(10)
        ->appends(request()->query())
        ->through(function ($factura) {
            return [
                'factura' => $factura->id_factura,
                'proveedor' => $factura->proveedor->razon_social ?? 'N/A',
                'total' => $factura->total,
                'items' => $factura->items,
                'fecha' => $factura->fecha,
                'tipo' => $factura->proveedor->tipo,
                'spanTipo' => $factura->proveedor->spanTipo,
                'codigo' => $factura->codigo??'--'
            ];
        });

        $headers = ['ID Factura', 'Codigo', 'Proveedor', 'Tipo Proveedor', 'Fecha Pedido', 'Cantidad de Items', 'Total', 'Opciones'];
        $headerFactura = ['ID', 'Num Orden o Factura', 'Nombre Item', 'Cantidad', 'Valor unidad','Fecha Pedido'];

        return view('pedidos.index', compact('title', 'items', 'headers', 'headerFactura'));
    }

    public function create( )
    {
        Gate::authorize('pedidos.create');
        return $this->form();
    }

    public function edit($id)
    {
        Gate::authorize('pedidos.edit');
        return $this->form($id);
    }

    public function form($id = null)
    {
        $title = $id?'Editar Pedido de Compra':'Crear Pedido de Compra';
        $pedidos = $id?Pedidos::where('id_factura', $id)->get():null;
        $materiales = InventarioMaterial::where('activo', 1)->get()->toArray();
        $insumos = Insumos::where('estado', 1)->get()->toArray();
        $proyectos = Proyecto::wherein('id_estado', [1, 5])->get(['id', 'nombre_proyecto'])->toArray();
        $proveedor = Proveedor::wherein('activo', [1])->get(['id', 'razon_social', 'tipo'])->toArray();
        $tipos = Proveedor::$tipo;
        return view('pedidos.create', compact('title', 'pedidos', 'materiales', 'proyectos', 'proveedor', 'tipos', 'insumos'));
    }

    public function save(Request $request)
    {
        Gate::authorize('pedidos.save');
        $validated = $request->validate([
            'fecha' => 'required|date_format:Y-m-d',
            'codigo' => 'required|string',
            'id_proveedor' => [
                'required',
                'integer',
                Rule::exists('inventario_proveedores', 'id'),
            ],
            'id_proyecto' => [
                'nullable',
                'integer',
                Rule::exists('proyectos', 'id'),
            ],
            'materiales' => ['required', 'array', 'min:1'],
            'materiales.*.id_material' => [
                'required',
                'integer'
            ],
            'materiales.*.cantidad' => [
                'required',
                'integer',
                'min:1'
            ],
            'materiales.*.valor_unidad' => ['required', 'integer'],
            'materiales.*.valor_compra' => ['required', 'integer'],
            'tipo' => ['required', Rule::in(array_keys(Proveedor::$tipo))],
        ]);

        // Iniciar transacción
        return DB::transaction(function () use ($validated) {

            $factura = Pedidos::max('id_factura') + 1;
            $dato = [
                'id_factura' => $factura,
                'codigo' => $validated['codigo'],
                'fecha' => $validated['fecha'],
                'id_proveedor' => $validated['id_proveedor'],
                'tipo' => $validated['tipo']
            ];

            // Proceso pedido
            foreach ($validated['materiales'] as $material) {

                // Crear el despacho
                $dato['id_material'] = $material['id_material'];
                $dato['cantidad'] = $material['cantidad'];
                $dato['vr_unidad'] = $material['valor_unidad'];
                $dato['vr_compra'] = $material['valor_compra'];
                Pedidos::create($dato);

                if($validated['tipo'] == 1){
                    $inventarioMaterial = InventarioMaterial::find($material['id_material']);
                    $inventarioMaterial['valor_unidad'] = $material['valor_unidad'];
                    $inventarioMaterial['valor_inventario'] = $material['valor_compra'];
                    if(!isset($validated['id_proyecto'])){
                        $inventarioMaterial->increment('cantidad', $material['cantidad']);
                    }
                    $inventarioMaterial->save();
                } else {
                    Insumos::find($material['id_material'])->increment('cantidad', $material['cantidad']);
                }
            }

            // Proceso despacho si existe id proyecto
            if(isset($validated['id_proyecto']) && $validated['tipo'] == 1){
                $codigo1 = Despachos::generarCodigoUnico();
                $dato = [
                    'tipo' => 1,
                    'codigo' => $codigo1,
                    'id_user' => Auth::user()->id,
                    'id_proyecto' => $validated['id_proyecto'],
                ];
                // Procesar materiales
                foreach ($validated['materiales'] as $material) {
                    // Crear el despacho
                    $dato['id_material'] = $material['id_material'];
                    $dato['cantidad'] = $material['cantidad'];
                    $dato['valor_unidad'] = $material['valor_unidad'];
                    $dato['valor_inventario'] = $material['valor_compra'];
                    Despachos::create($dato);
                }
            }
            $codigo = $validated['codigo'];
            return redirect()->route('pedidos.index')->with('success', "Pedido con orden: {$codigo} fue registrado correctamente");
        });
    }

    public function listPedido( )
    {
        Gate::authorize('pedidos.listPedido');
        try {
            $listPedido = Pedidos::with('material')->where('id_factura', intval( Request('id') ) )
            ->orderBy('createdAt', 'desc')
            ->paginate(10)
            ->through(function ($data) {
                return [
                    'id' => $data->id_material,
                    'cantidad' => $data->cantidad,
                    'valor_unidad' => $data->vr_unidad,
                    'fecha' => explode(' ', $data->fecha)[0],
                    'nombre_material' => $data->tipo == 1? $data->material->nombre_material : $data->material->nombre_insumo,
                    'codigo' => $data->codigo
                ];
            });

            return response()->json([
                'status' => true,
                'message' => 'Lista de pedido.',
                'data' => $listPedido
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener la lista.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function hPedidoproveedor( $id )
    {
        Gate::authorize('pedidos.hPedidoproveedor');
        try {
            $arrhistorial = Pedidos::where('id_proveedor', intval( $id ) )->whereHas('material', function ($query) {
                $query->where('activo', 1);
            })->pluck('id_material')->unique()->toArray();
            if(!$arrhistorial){
                return response()->json([
                    'status' => true,
                    'message' => 'No hay historial de pedidos para este proveedor.',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'Historial de pedidos por proveedor.',
                'data' => $arrhistorial
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener el historial.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
