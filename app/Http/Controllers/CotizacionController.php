<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Cotizacion;

class CotizacionController extends Controller
{
    public function index( ) 
    {
        $title = 'Lista de Cotizacion';
        $items = Cotizacion::with(['proyecto'])
        ->selectRaw('id_proyecto, 
                    DATE(createdAt) as createdAt,
                    SUM(cantidad * valor_unidad) as total,
                    COUNT(*) as items')
        ->when(request('fecha'), function ($query, $fecha) {
            return $query->whereDate('createdAt', '>=', $fecha);
        })
        ->when(request('fecha'), function ($query, $fecha) {
            return $query->whereDate('createdAt', '<=', $fecha);
        })
        ->when(request('nomProyecto'), function ($query, $nomProyecto) {
            return $query->whereHas('proyecto', function ($q) use ($nomProyecto) {
                $q->where('nombre_proyecto', 'like', "%{$nomProyecto}%");
            });
        })
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
        
        $headers = ['Nombre del Proyecto', 'Fecha Cotizacion', 'Numero de Items', 'Total Cotizado'];
        return view('cotizacion.index', compact('title', 'items', 'headers'));
    }
}