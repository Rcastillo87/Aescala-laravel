<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use App\Models\Otrosi;
use App\Models\Proyecto;
use App\Models\Pagos;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class CarteraController extends Controller
{
    public function index($id_proyecto = '')
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

        return view('cartera.index', compact( 'title', 'proyectos', 'id_proyecto'));
    }

    public function indexPagosProyecto($id){
        return $this->index($id);
    }

    public function pagosProyecto($id)
    {
        Gate::authorize('cartera.pagosProyecto');
        return $this->datapagosProyecto($id);
    }

    private function datapagosProyecto($id){
        try {
            $proyecto = Proyecto::with('otro_si')->findOrFail($id);
            $selectPro = $proyecto->pagos;
            $selectOtrosi = $proyecto->otro_si()->get()
                ->map(function ($item) {
                    return [
                        'id_tipo' => $item->id,
                        'msg' => "Otro Si N° " . $item->numero,
                        'campo' => '',
                        'tipo_pago' => 2
                    ];
                })->toArray();

            $select = array_merge($selectPro, $selectOtrosi);

            $porcentProyec = [
                $proyecto->termino_1_por,
                $proyecto->termino_2_por,
                $proyecto->termino_3_por,
                $proyecto->termino_4_por,
                $proyecto->termino_5_por,
                $proyecto->termino_6_por,
            ];

            $valProyecto = $proyecto->total;
            $valOtrosis = $proyecto->TotalOtroSi?? 0;
            $valTotodal = $proyecto->total + $valOtrosis;

            $contraProyec = [
                "id_proyecto" => $id,
                "id_pago" => $id,
                "tipo" => 1,
                "concepto" => "Contrato Proyecto",
                "valor_total" => $valProyecto,
                "urlContrato" => route('proyecto.contratoPdf', $id),
                "pazysalvo" => $proyecto->paz_salvo
            ];
            $contraOtrosi = $proyecto->otro_si()
                ->where('estado', 1)
                ->get()
                ->map(function ($item) use ($id) {
                    return [
                        "id_proyecto" => $id,
                        "id_pago" => $item->id,
                        "tipo" => 2,
                        "concepto" => "Otro Si N° " . $item->numero,
                        "valor_total" => $item->total_deve,
                        "urlContrato" => route('otro_si.otroSiPdf', $item->id),
                        "pazysalvo" => $item->paz_salvo
                    ];
                })
                ->toArray();
            $contratos = array_merge([$contraProyec], $contraOtrosi);

            $valTotalPagado = Pagos::where('id_proyecto', $id)->sum('valor_pagado');

            $porcenTx = Proyecto::$porcenTX;
            $pagos = Pagos::with(['proyecto', 'otro_si', 'soporte'])
                ->where('id_proyecto', $id)
                ->get()
                ->map(function ($item) use ($porcenTx, $id) {
                    if ($item->tipo_pago == 1) {
                        $campo = "termino_{$item->concepto}_por";
                        $concepto = 'Porcentaje: '
                            . ($item->proyecto?->$campo ?? 0)
                            . '% '
                            . ($porcenTx[$item->concepto] ?? '');
                    } else {
                        $concepto = 'Otro Sí N° ' . ($item->otro_si?->numero ?? '');
                    }
                    return [
                        "id" => $item->id,
                        "id_proyecto" => $item->id_proyecto,
                        "id_pago" => $item->id_pago,
                        "tipo_pago" => $item->tipo_pago,
                        "concepto" => $concepto,
                        "valor_pago" => $item->valor_pagado,
                        "factura" => $item->fv,
                        "fecha_pago" => $item->fecha_pago,
                        "comentarios" => $item->comentario,
                        "urlRecivo" => route('cartera.reciboPDF', $item->id),
                        "soporte" => $item->soporte,
                        "url_soporte" => $item->soporte? route('cartera.viewDocumento', $item->soporte->id) : ''
                    ];
                })
                ->toArray();

            $agrupadoPagosProyecto = [
                'Contrato',
                $porcentProyec[0] * $valProyecto/100,
                $porcentProyec[1] * $valProyecto/100,
                $porcentProyec[2] * $valProyecto/100,
                $porcentProyec[3] * $valProyecto/100,
                $porcentProyec[4] * $valProyecto/100,
                $porcentProyec[5] * $valProyecto/100
            ];

            $agrupadoPagosOtrosi = Otrosi::where('id_proyecto', $id)
                    ->orderBy('id', 'desc')
                    ->get()
                    ->map(function ($item) {
                        $valor = (float) $item->totalDeve;
                        $posicion1 = 0;
                        $posicion2 = 0;
                        if ($valor < 10000000) {
                            $posicion1 = $valor;
                            $posicion2 = 0;
                        } else {
                            $posicion1 = $valor / 2;
                            $posicion2 = $valor / 2;
                        }
                        return [
                            'Otro Sí N° ' . $item->numero,
                	        0,
                            $posicion1,
                            $posicion2,
                            0,
                            0,
                            0
                        ];
                    })->toArray();

            return response()->json([
                'status' => true,
                'message' => 'Consulta exitosa',
                'data' => [
                    "pagos" => $pagos,
                    "total_pagos" => Pagos::where('id_proyecto', $id)->sum('valor_pagado'),
                    "resumen" => $contratos,
                    "total_proyecto" => $valTotodal,
                    "total_pagado" => $valTotalPagado,
                    "porcentajes" => $porcentProyec,
                    "relacion_pagos" => array_merge([$agrupadoPagosProyecto], $agrupadoPagosOtrosi),
                    "select" => $select
                ]
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Error al consultar pagos.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function save(Request $request)
    {
        Gate::authorize('cartera.save');
        $val = [
            'id_proyecto'  => ['required', 'integer', Rule::exists('proyectos', 'id')],
            'id_tipo'      => 'required|integer',
            'tipo'         => 'required|integer|in:1,2',
            'valor_pagado' => 'required|numeric|min:0',
            'comentario'   => 'nullable|string|max:500',
            'fv'           => 'nullable|string|max:20',
            'fecha_pago'   => 'required|date',
            'concepto'     => 'nullable|integer'
        ];

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
                'id_proyecto'  => $request->id_proyecto,
                'tipo_pago'    => $request->tipo,
            ])->max('rc') + 1;

            $pago = Pagos::create([
                'id_proyecto'  => $request->id_proyecto,
                'tipo_pago'    => $request->tipo,
                'id_pago'      => $request->id_tipo,
                'valor_pagado' => $request->valor_pagado,
                'fecha_pago'   => $request->fecha_pago,
                'comentario'   => $request->comentario ?? '',
                'concepto'     => $request->concepto,
                'fv'           => $request->fv,
                'rc'           => $rc,
                'id_user'      => Auth::id(),
            ]);

            if ($pago->valance && $request->tipo == 1) {
                Proyecto::find($request->id_proyecto)->update(['paz_salvo' => 1]);
            }

            if ($pago->valanceOtroSi && $request->tipo == 2) {
                Otrosi::find($request->id_tipo)->update(['paz_salvo' => 1]);
            }

            DB::commit();
            return response()->json([
                'status'  => true,
                'url' => route('cartera.indexPagosProyecto', $request->id_proyecto),
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

    public function certificadoPZPDF($id_pago, $tipo)
    {
        try {
            $pago = Pagos::with('proyecto', 'otro_si')->where(['id_pago' => $id_pago, 'tipo_pago' => $tipo])->first();
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
            return $pdf->stream('certificadoPZPDF-' . $id_pago . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Error generando certificado PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    public function reciboPDF($id)
    {
        Gate::authorize('cartera.reciboPDF');

        // 1. Usamos get() para obtener una colección y poder mapear después
        $pagosQuery = Pagos::find($id);

        if (!$pagosQuery) {
            abort(404, 'El pago no existe');
        }

        $proyecto = $pagosQuery->proyecto;
        $totalPago = $pagosQuery->valor_pagado;

        if ($pagosQuery->tipo_pago == 1) {
            $descripcion = collect($proyecto->Pagos)->where('campo', $pagosQuery->concepto)->first()['msg'] ?? 'Pago de Proyecto';
        } else {
            $descripcion = 'Pago Otro Si # ' . ($pagosQuery->otro_si->numero ?? '');
        }

        $pagosQuery->descripcion = $descripcion;

        // Carga de Geografía
        $pathJson = storage_path('json/jsonCityColombia.json');
        $dptArray = json_decode(file_get_contents($pathJson), true);

        $ciudad_dpt = $dptArray[$proyecto->departamento]['departamento'] . ', ' .
                      $dptArray[$proyecto->departamento]['ciudades'][$proyecto->ciudad];

        $data = [
            'items'         => [$pagosQuery],
            'proyecto'      => $proyecto,
            'ciudad_dpt'    => $ciudad_dpt,
            'tipo_doc_acro' => Proyecto::$tipoDocumento[$proyecto->tipo_doc_cliente][0] ?? '',
            'total_pago'    => $totalPago,
            'logo'          => public_path('img/logo.png')
        ];

        $pdf = Pdf::loadView('cartera.reciboPDF', $data)
            ->setPaper('letter', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'DejaVu Sans'
            ]);

        return $pdf->stream('recibo-'.$id.'.pdf');
    }

    public function deletePago($id)
    {
        Gate::authorize('cartera.deletePago');

        $pago = Pagos::findOrFail($id);
        $tipo_pago = $pago->tipo_pago;
        $id_tipo = $pago->id_tipo; // El ID del Proyecto o del OtroSí
        $id_proyecto = $pago->id_proyecto; // El ID del Proyecto o del OtroSí
        DB::beginTransaction();
        try {
            $pago->delete();
            if ($tipo_pago == 1) {
                $proy = Proyecto::find($id_tipo);
                if ($proy) {
                    $pazYSalvo = $proy->valance ? 1 : 0;
                    $proy->update(['paz_salvo' => $pazYSalvo]);
                }
            }

            if ($tipo_pago == 2) {
                $otroSi = Otrosi::find($id_tipo);
                if ($otroSi) {
                    $pazYSalvo = $otroSi->valanceOtroSi ? 1 : 0;
                    $otroSi->update(['paz_salvo' => $pazYSalvo]);
                }
            }

            DB::commit();
            return response()->json([
                'status'  => true,
                'message' => 'Pago eliminado correctamente.',
                'url' => route('cartera.indexPagosProyecto', $id_proyecto),
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

    public function viewDocumento($id)
    {
        $doc = Documento::findOrFail($id);
        return response($doc->contenido_descomprimido)
                ->header('Content-Type', $doc->mime_type)
                ->header('Content-Disposition', 'inline; filename="' . $doc->nombre . '"');
    }

}
