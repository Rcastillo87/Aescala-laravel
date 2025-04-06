<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Models\Pedidos;
use App\Models\InventarioMaterial;
use App\Models\Proyecto;
use App\Models\Proveedor;

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
        ->when(request('fecha_desde'), function ($query, $fecha) {
            return $query->whereDate('fecha', '>=', $fecha);
        })
        ->when(request('fecha_hasta'), function ($query, $fecha) {
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
        return view('pedidos.index', compact('title', 'items', 'headers'));
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
        ->get(['id','nombre_material', 'cantidad', 'valor_unidad', 'spanTipo', 'unidades', 'id_unidad', 'tipo', 'descripccion'])
        ->toArray();
        $proyectos = Proyecto::wherein('id_estado', [1, 5])
        ->get(['id', 'nombre_proyecto'])
        ->toArray();
        $proveedor = Proveedor::wherein('activo', [1])
        ->get(['id', 'razon_social'])
        ->toArray();
        
        return view('pedidos.create', compact('title', 'pedidos', 'materiales', 'proyectos', 'proveedor'));
    }

}