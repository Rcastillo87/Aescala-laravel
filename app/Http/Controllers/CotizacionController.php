<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Cotizacion;
use App\Models\SolicitudMaterial;
use Barryvdh\DomPDF\Facade\Pdf;

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
        ->appends(request()->query())
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

    public function PDFCotizacion($id){
        $proyecto = SolicitudMaterial::find($id)->proyecto;
        $datos = (new Cotizacion)->dataCotizacion($id);

        $datosFactura = [
            'empresa' => [
                'razon' => env('RAZON', 'AESCALA'),
                'nit' => env('NIT', '901.451.774-2'),
                'telefono' => env('TEL', '323-345-0903'),
                'direccion' => env('DIREC', 'Dirección: carrera 1d #46-63'),
                'logo' => public_path('img/logo.png')
            ],
            'despacho' => $datos,
            'proyecto' => $proyecto,
            'cotizacion' => true
        ];

        $pdf = Pdf::loadView('proyecto.factura', $datosFactura);
        if (Request('view')) {
            return $pdf->stream('factura-' . $id . '.pdf');
        }
        return $pdf->download('factura-' . $id . '.pdf');
    }
}
