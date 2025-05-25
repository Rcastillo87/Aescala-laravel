<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Models\Pedidos;
use App\Models\InventarioMaterial;
use App\Models\Proyecto;
use App\Models\Proveedor;
use App\Models\Despachos;
use Illuminate\Support\Facades\Auth;

class PedidosController extends Controller
{

    public function index( ) 
    {
        $title = 'Lista de Pedidos';
        $items = Pedidos::with(['proveedor', 'material'])
        ->selectRaw('id_factura, 
                    DATE(fecha) as fecha,
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
        ->through(function ($factura) {
            return [
                'factura' => $factura->id_factura,
                'proveedor' => $factura->proveedor->razon_social ?? 'N/A',
                'total' => $factura->total,
                'items' => $factura->items,
                'fecha' => $factura->fecha
            ];
        });
        
        $headers = ['ID Factura y/o Orden', 'Proveedor', 'Fecha Pedido', 'Cantidad de Items', 'Total', 'Opciones'];
        $headerFactura = ['ID', 'Nombre Item', 'Cantidad', 'Valor unidad','Fecha Pedido'];

        return view('pedidos.index', compact('title', 'items', 'headers', 'headerFactura'));
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
        $title = $id?'Editar Pedido':'Crear Pedido';
        $pedidos = $id?Pedidos::where('id_factura', $id)->get():null;
        $materiales = InventarioMaterial::where('activo', 1) 
        ->get()
        ->toArray();
        $proyectos = Proyecto::wherein('id_estado', [1, 5])
        ->get(['id', 'nombre_proyecto'])
        ->toArray();
        $proveedor = Proveedor::wherein('activo', [1])
        ->get(['id', 'razon_social'])
        ->toArray();
        
        return view('pedidos.create', compact('title', 'pedidos', 'materiales', 'proyectos', 'proveedor'));
    }

    public function save(Request $request)
    {

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
                'integer',
                Rule::exists('inventario_materiales', 'id'),
                function ($attribute, $value, $fail) {
                    $material = InventarioMaterial::find($value);
                    if (!$material || $material->activo != 1) {
                        $fail("El material seleccionado no está disponible.");
                    }
                }
            ],
            'materiales.*.cantidad' => [
                'required',
                'integer',
                'min:1'
            ],
            'materiales.*.valor_unidad' => ['required', 'integer']
        ]);

        // Iniciar transacción
        return DB::transaction(function () use ($validated) {
            
            $factura = Pedidos::max('id_factura') + 1;
            $dato = [
                'id_factura' => $factura,
                'codigo' => $validated['codigo'],
                'fecha' => $validated['fecha'],
                'id_proveedor' => $validated['id_proveedor'],
            ];

            // Proceso pedido
            foreach ($validated['materiales'] as $material) {

                // Crear el despacho
                $dato['id_material'] = $material['id_material'];
                $dato['cantidad'] = $material['cantidad'];
                $dato['vr_unidad'] = $material['valor_unidad'];
                Pedidos::create($dato);
                
                $inventarioMaterial = InventarioMaterial::find($material['id_material']);
                $inventarioMaterial['valor_unidad'] = $material['valor_unidad'];
                if(!isset($validated['id_proyecto'])){
                    $inventarioMaterial->increment('cantidad', $material['cantidad']);
                }
                $inventarioMaterial->save();
            }
            
            // Proceso despacho si existe id proyecto
            if(isset($validated['id_proyecto'])){
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
                    Despachos::create($dato);
                }
            }
            $codigo = $validated['codigo'];
            return redirect()->route('proveedor.index')
                        ->with('success', "Pedido con orden: {$codigo} fue registrado correctamente");
        });
    }

    public function listPedido( ) 
    {
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
                    'nombre_material' => $data->material->nombre_material
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

}