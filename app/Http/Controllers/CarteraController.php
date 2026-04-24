<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use App\Models\Otrosi;
use App\Models\Proyecto;
use App\Models\Pagos;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CarteraController extends Controller
{
    public function index( )
    {
        Gate::authorize('cartera.index');
        $title = 'Lista de Cartera';
        $usuario = Auth::user();

        $proyectos = Proyecto::whereIn('id_estado', [1, 3, 5])
            ->whereNotNull('cedula_cliente')
            ->whereNotNull('tipo_doc_cliente')
            ->orderBy('nombre_proyecto', 'ASC')
            ->get(['id', 'nombre_proyecto'])
            ->toArray();

        return view('cartera.index', compact( 'title', 'proyectos'));
    }

    public function save(Request $request)
    {
        Gate::authorize('cartera.save');
        $val = [
            'proyecto_id'  => 'required|integer',
            'tipo'         => 'required|integer|in:1,2',
            'valor_pagado' => 'required|numeric|min:0',
            'comentario'   => 'nullable|string|max:500',
            'fv'           => 'nullable|string|max:20',
            'fecha_pago'   => 'required|date',
        ];

        if($request->tipo == 1){
            $val = array_merge($val, ['concepto'     => 'required|integer|between:1,6']);
        }

        $validator = Validator::make($request->all(), $val, [
            'required' => 'Este campo es obligatorio.',
            'integer'  => 'Debe ser un número válido.',
            'numeric'  => 'Debe ser un valor numérico.',
            'between'  => 'Valor fuera del rango permitido.',
            'in'       => 'Valor inválido.',
            'min'      => 'El valor debe ser mayor a 0.',
            'date'     => 'Fecha inválida.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Hay errores en el formulario.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $rc = Pagos::where([
                'id_proyecto'  => $request->proyecto_id,
                'tipo_pago'    => $request->tipo,
            ])->max('rc') + 1;

            $pago = Pagos::create([
                'id_proyecto'  => $request->proyecto_id,
                'tipo_pago'    => $request->tipo,
                'valor_pagado' => $request->valor_pagado,
                'fecha_pago'   => $request->fecha_pago,
                'comentario'   => $request->comentario ?? '',
                'concepto'     => $request->concepto,
                'fv'           => $request->fv,
                'rc'           => $rc,
                'id_user'      => Auth::id(),
            ]);

            if ($pago->valance && $request->tipo == 1) {
                Proyecto::find($request->proyecto_id)->update(['paz_salvo' => 1]);
            }

            if ($pago->valanceOtroSi && $request->tipo == 2) {
                Otrosi::find($request->proyecto_id)->update(['paz_salvo' => 1]);
            }

            DB::commit();
            return response()->json([
                'status'  => true,
                'message' => 'Pago registrado correctamente.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Error al registrar el pago.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function pagosProyecto($id)
    {
        Gate::authorize('cartera.pagosProyecto');

        $porcenTx = Proyecto::$porcenTX;
        $proyecto = Proyecto::with('otro_si')->find($id);
        $porcentProyec = [
            $proyecto->termino_1_por,
            $proyecto->termino_2_por,
            $proyecto->termino_3_por,
            $proyecto->termino_4_por,
            $proyecto->termino_5_por,
            $proyecto->termino_6_por,
        ];

        $valProyecto = $proyecto->total;

        $valTotalPagado = Pagos::where(['tipo_pago' => 1, 'id_proyecto' => $id])->sum('valor_pagado');

        $pagos = Pagos::with(['proyecto', 'otro_si'])
                ->where('id_proyecto', $id)
                ->get()
                ->map(function ($item) use ($porcenTx) {
                    $campo = "termino_{$item->concepto}_por";
                    return [
                        "id" => $item->id,
                        "concepto" => ($item->tipo_pago == 1)
                            ? 'Porcentaje: ' . ($item->proyecto?->$campo ?? 0) . '% ' . ($porcenTx[$item->concepto] ?? '')
                            : 'Otro Si N° ' . ($item->numero ?? ''),
                        "valor_pago" => $item->valor_pagado,
                        "factura" => $item->fv,
                        "fecha_pago" => $item->fecha_pago,
                        "comentarios" => $item->comentario,
                    ];
                })->toArray();

        $contraProyec = [
            "id_proyecto" => $id,
            "tipo" => 1,
            "concepto" => "Contrato Proyecto",
            "valor_total" => $valProyecto
        ];

        $contraOtrosi = $proyecto->otro_si()
            ->where('estado', 1)
            ->get()
            ->map(function ($item) {
                return [
                    "id_proyecto" => $item->id_proyecto,
                    "tipo" => 2,
                    "concepto" => "Otro Si N° " . $item->numero,
                    "valor_total" => $item->TotalDeve,
                ];
            })
            ->toArray();

        $contratos = array_merge($contraProyec, $contraOtrosi);

        return response()->json([
            'status' => true,
            'message' => 'Consulta exitosa',
            'data' => [
                "pagos" => $pagos,
                "total_pagos" => Pagos::sum('valor_pagado'),
                "resumen" => $contratos,
                "total_proyecto" => $valProyecto,
                "total_pagado" => $valTotalPagado,
                "porcentajes" => $porcentProyec,
                "relacion_pagos" => [
                    "contrato" => [400000, 800000, 1200000, 1600000, 2000000],
                    "totales" => [100000, 200000, 300000, 400000, 500000]
                ]
            ]
        ], 200);
    }

    public function certificadoPZPDF($idProyecto, $tipo)
    {
        try {
            $pago = Pagos::with('proyecto', 'otro_si')->where(['id_proyecto' => $idProyecto, 'tipo_pago' => $tipo])->first();
            $carbon = \Carbon\Carbon::parse($pago->proyecto->fecha_firma);
            $carbon->locale('es');
            $fechaTexto = $carbon->translatedFormat('d \d\e F \d\e Y');

            // Firma representante (igual que ya haces)
            $path = public_path('img/firmaRepre.png');

            if (file_exists($path)) {
                $imageData = file_get_contents($path);
                $imageInfo = getimagesize($path);
                $mime = $imageInfo['mime'];
                $base64 = 'data:' . $mime . ';base64,' . base64_encode($imageData);
            } else {
                $base64 = null;
            }

            $data = [
                "id_proyecto"       => $pago->proyecto->id,
                "fecha_contrato"    => mb_strtoupper($fechaTexto, 'UTF-8'),
                "nombre_cliente"    => \Illuminate\Support\Str::title($pago->proyecto->nombre_cliente),
                "tipo_doc_cliente"  => Proyecto::$tipoDocumento[$pago->proyecto->tipo_doc_cliente][1] ?? '',
                "documento_cliente" => number_format($pago->proyecto->cedula_cliente, 0, ',', '.'),
                "imgRepre"          => $base64,
            ];

            $pdf = PDF::loadView('cartera.certificadoPZPDF', $data);
            return $pdf->stream('certificadoPZPDF-' . $idProyecto . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error generando certificado PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    public function reciboPDF($id, $tipo)
    {
        Gate::authorize('cartera.reciboPDF');
        $items = Pagos::where([
            'id_proyecto' => $id,
            'tipo_pago'   => $tipo,
        ])->get();

        if ($items->isEmpty()) {
            abort(404, 'No hay pagos para este recibo');
        }

        if ($tipo == 1) {
            $proyecto = $items->first()->proyecto;
            $pagos = $proyecto->Pagos;
            $totalPago = $proyecto->totalPagado;
        } else {
            $otroSi = $items->first()->otro_si;
            $proyecto = $otroSi->proyecto;
            $pagos = '';
            $totalPago = $otroSi->totalPago;
        }

        $dptArray = json_decode(
            file_get_contents(storage_path('json/jsonCityColombia.json')),
            true
        );

        $ciudad_dpt = $dptArray[$proyecto->departamento]['departamento'] . ', ' .
                    $dptArray[$proyecto->departamento]['ciudades'][$proyecto->ciudad];

        $items = $items->map(function ($item) use ($pagos) {

            if($item->tipo_pago == 1) {
                $descripcion = collect($pagos)->map(function ($concepto) use ($item) {
                    if ($concepto['termino'] == $item->concepto) {
                        return $concepto['msg'];
                    }
                })->filter()->first();
            } else {
                $descripcion = 'Pago Otro Si # ' . $item->otro_si->numero;
            }

            return (object) [
                'rc'           => $item->rc,
                'fecha_pago'   => $item->fecha_pago,
                'descripcion'  => $descripcion,
                'valor_pago'   => $item->valor_pagado,
                'comentario'   => $item->comentario
            ];
        });

        $data = [
            'items'    => $items,
            'proyecto' => $proyecto,
            'ciudad_dpt'    => $ciudad_dpt,
            'tipo_doc_acro' => Proyecto::$tipoDocumento[$proyecto->tipo_doc_cliente][0] ?? '',
            'total_pago'    => $totalPago,
            'logo' => public_path('img/logo.png')
        ];

        $pdf = Pdf::loadView('cartera.reciboPDF', $data)
            ->setPaper('letter', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->stream('recibo-'.$id.'.pdf');
    }

    public function deletePago($id)
    {
        Gate::authorize('cartera.deletePago');
        $pago = Pagos::findOrFail($id);
        $tipo_pago = $pago->tipo_pago;
        $id_proyecto = $pago->id_proyecto;

        try {
            DB::beginTransaction();

            $pago->delete();

            if ($tipo_pago == 1) {
                $proyecto = Proyecto::find($id_proyecto);
                $totalPagado = $proyecto->totalPagado;

                if ($totalPagado < ($proyecto->total ?? 0)) {
                    $proyecto->update(['paz_salvo' => 0]);
                }
            } elseif ($tipo_pago == 2) {
                $otroSi = Otrosi::find($id_proyecto);
                $totalPagado = Pagos::where([
                    'id_proyecto' => $id_proyecto,
                    'tipo_pago'   => 2
                ])->sum('valor_pagado');

                if ($totalPagado < ($otroSi->totalDeve ?? 0)) {
                    $otroSi->update(['paz_salvo' => 0]);
                }
            }

            DB::commit();
            return response()->json([
                'status'  => true,
                'message' => 'Pago eliminado correctamente.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Error al eliminar el pago.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

}
