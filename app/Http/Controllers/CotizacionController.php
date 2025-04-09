<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\Models\Cotizacion;
use App\Models\InventarioMaterial;
use App\Models\Proyecto;
use App\Models\Proveedor;
use App\Models\Despachos;
use Illuminate\Support\Facades\Auth;

class CotizacionController extends Controller
{
    public function index( ) 
    {
        $title = 'Lista de Cotizacion';
        $items = Cotizacion::with(['proyecto', 'material'])
        ->selectRaw('id_proyecto, 
                    DATE(createdAt) as createdAt,
                    SUM(cantidad * valor_unidad) as total,
                    COUNT(*) as items')
        /*->when(request('factura'), function ($query, $factura) {
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
        })*/
        ->groupBy('id_proyecto', DB::raw('DATE(createdAt)'))
        ->orderBy('id_proyecto', 'desc')
        ->paginate(10)
        ->through(function ($cotizacion) {
            return [
                'id_proyecto' => $cotizacion->id_proyecto,
                'nombre_proyecto' => $cotizacion->proyecto->nombre_proyecto,
                'total' => $cotizacion->total,
                'items' => $cotizacion->items,
                'createdAt' => explode(' ', $cotizacion->createdAt)[0]
            ];
        });
        
        $headers = ['Nombre del Proyecto', 'Fecha Cotizacion', 'Numero de Items', 'Total Cotizado', 'Opciones'];
        return view('cotizacion.index', compact('title', 'items', 'headers'));
    }
}