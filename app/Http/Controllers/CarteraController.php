<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use App\Models\Otrosi;
use App\Models\Proyecto;
use App\Models\Pagos;
use App\Models\Documento;
use App\Models\PagoRefe;
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
            $proyecto = Proyecto::with(['otro_si', 'soporteFact'])->findOrFail($id);
            $selectPro = $proyecto->dataSelect;
            $selectOtrosi = $proyecto->otro_si()->get()
                ->map(function ($item) {
                    return $item->dataSelect;
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
                        "concepto" => "Otrosí N° " . $item->numero,
                        "valor_total" => $item->total_deve,
                        "urlContrato" => route('otro_si.otroSiPdf', $item->id),
                        "pazysalvo" => $item->paz_salvo
                    ];
                })
                ->toArray();
            $contratos = array_merge([$contraProyec], $contraOtrosi);

            $valTotalPagado = Pagos::where('id_proyecto', $id)->get()->sum('valorTotal');

            $pagos = Pagos::with(['proyecto', 'soporte', 'pago_refe.reference'])
                ->where('id_proyecto', $id)
                ->get()
                ->map(function ($item) {
                    $texto = $item->pago_refe
                        ->map(function ($ref) {
                            $data = $ref->reference?->data_select;
                            if (!$data) {
                                return null;
                            }
                            if (is_array($data) && isset($data[$ref->concepto])) {
                                return $data[$ref->concepto]['msg'];
                            }
                            return $data['msg'] ?? null;
                        })
                        ->filter()
                        ->implode(', ');
                    return [
                        "id" => $item->id,
                        "id_proyecto" => $item->id_proyecto,
                        "concepto" => $texto,
                        "valor_pago" => $item->valorTotal,
                        "fecha_pago" => $item->fecha_pago,
                        "comentarios" => $item->comentario,
                        "urlRecivo" => route('cartera.reciboPDF', $item->id),
                        "soporte" => $item->soporte,
                        "url_soporte" => $item->soporte? route('cartera.viewDocumento', $item->soporte->id) : '',
                        "rc" => $item->rc,
                    ];
                })
                ->toArray();

            //valance proyecto
            $lineDeveProy = [
                $porcentProyec[0] * $valProyecto/100,
                $porcentProyec[1] * $valProyecto/100,
                $porcentProyec[2] * $valProyecto/100,
                $porcentProyec[3] * $valProyecto/100,
                $porcentProyec[4] * $valProyecto/100,
                $porcentProyec[5] * $valProyecto/100
            ];

            $lineaPagoPro = PagoRefe::whereHas('pago', function ($q) use ($id) {
                $q->where('id_proyecto', $id);
            })
            ->where('reference_type', 'App\Models\Proyecto')
            ->selectRaw('SUM(valor) as total')
            ->groupBy('concepto')
            ->orderBy('concepto', 'ASC')
            ->get()
            ->pluck('total')->toArray();

            if(empty($lineaPagoPro)){
                $lineaPagoPro = [0, 0, 0, 0, 0, 0];
            }

            $lineBalancePro = array_map(function($a, $b) {
                return (float)$a - (float)$b;
            }, $lineDeveProy, $lineaPagoPro);

            $dataBalancePro = array_merge([$lineDeveProy], [$lineaPagoPro], [$lineBalancePro]);

            //valance otrosi
            $lineDeveOtrosi = Otrosi::where('id_proyecto', $id)
                ->orderBy('numero', 'ASC')
                ->get()
                ->pluck('total_deve', 'numero')
                ->toArray();

            $linePagoOtrosi = Otrosi::where('id_proyecto', $id)
                ->orderBy('numero', 'ASC')
                ->get()
                ->pluck('total_pago', 'numero')
                ->toArray();

            if(empty($linePagoOtrosi)){
                $cant = count($lineDeveOtrosi);
                $linePagoOtrosi = array_fill(1, $cant, 0);
            }

            $lineBalanceOtrosi = array_map(function($a, $b) {
                return (float)$a - (float)$b;
            }, $lineDeveOtrosi, $linePagoOtrosi);
            $lineBalanceOtrosi = array_combine(array_keys($lineDeveOtrosi), $lineBalanceOtrosi);

            $dataBalanceOtrosi = [
                array_values($lineDeveOtrosi),
                array_values($linePagoOtrosi),
                array_values($lineBalanceOtrosi)
            ];

            return response()->json([
                'status' => true,
                'message' => 'Consulta exitosa',
                'data' => [
                    "pagos" => $pagos,
                    "total_proyecto" => $valTotodal,
                    "total_pagado" => $valTotalPagado,
                    "resumen" => $contratos,
                    "porcentajes" => $porcentProyec,
                    "valance_pro" => $dataBalancePro,
                    "valance_otrosi" => $dataBalanceOtrosi,
                    "select" => $select,
                    "soporteFact" => $proyecto->soporteFact
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
            'comentario'   => 'nullable|string|max:500',
            'fecha_pago'   => 'required|date',
            'referencias' => [ 'required', 'array', 'min:1'],
            'referencias.*.reference_type' => [ 'required', 'string'],
            'referencias.*.reference_id' => [ 'required', 'integer'],
            'referencias.*.concepto' => [ 'nullable'],
            'referencias.*.valor' => [ 'required', 'numeric', 'min:1'],
        ];

        $validator = Validator::make($request->all(), $val, [
            'required' => 'Este campo es obligatorio.',
            'integer'  => 'Debe ser un número válido.',
            'numeric'  => 'Debe ser un valor numérico.',
            'between'  => 'Valor fuera del rango permitido.',
            'array'    => 'Formato inválido.',
            'min'      => 'El valor debe ser mayor a 0.',
            'date'     => 'Fecha inválida.',
        ]);

        $validator->after(function ($validator) use ($request) {
            $referencias = $request->referencias ?? [];
            $combinaciones = [];
            foreach ($referencias as $index => $item) {
                $key =
                    ($item['reference_type'] ?? '') . '|' .
                    ($item['reference_id'] ?? '') . '|' .
                    ($item['concepto'] ?? '');
                if (in_array($key, $combinaciones)) {
                    $validator->errors()->add(
                        "referencias.$index.reference_id",
                        'La referencia está repetida.'
                    );
                }
                $combinaciones[] = $key;
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Hay errores en el formulario.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();
            $pago = Pagos::create([
                'id_proyecto'  => $request->id_proyecto,
                'fecha_pago'   => $request->fecha_pago,
                'comentario'   => $request->comentario ?? '',
                'id_user'      => Auth::id(),
            ]);

            foreach ($request->referencias as $item) {
                $referencia = $pago->pago_refe()->create([
                    'reference_type' => $item['reference_type'],
                    'reference_id'   => $item['reference_id'],
                    'concepto'       => $item['concepto'],
                    'valor'          => $item['valor'],
                ]);

                if ($referencia->reference_type === Proyecto::class) {
                    $proyecto = Proyecto::find($referencia->reference_id);
                    if ($proyecto && $proyecto->valance) {
                        $proyecto->update([
                            'paz_salvo' => 1
                        ]);
                    }
                }

                if ($referencia->reference_type === Otrosi::class) {
                    $otroSi = Otrosi::find($referencia->reference_id);
                    if ($otroSi && $otroSi->valance) {
                        $otroSi->update([
                            'paz_salvo' => 1
                        ]);
                    }
                }
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

    public function certificadoPZPDF($id)
    {
        try {

            $proy = Proyecto::find($id);
            $carbon = \Carbon\Carbon::parse($proy->fecha_firma);
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
                "id_proyecto"       => $proy->id,
                "fecha_contrato"    => mb_strtoupper($fechaTexto, 'UTF-8'),
                "nombre_cliente"    => \Illuminate\Support\Str::title($proy->nombre_cliente),
                "tipo_doc_cliente"  => Proyecto::$tipoDocumento[$proy->tipo_doc_cliente][1] ?? '',
                "documento_cliente" => number_format($proy->cedula_cliente, 0, ',', '.'),
                "imgRepre"          => $base64,
            ];

            $pdf = PDF::loadView('cartera.certificadoPZPDF', $data);
            return $pdf->stream('certificadoPZPDF-' . $id . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Error generando certificado PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    public function reciboPDF($id)
    {
        Gate::authorize('cartera.reciboPDF');

        // 1. Usamos get() para obtener una colección y poder mapear después
        $pagosQuery = Pagos::with('pago_refe.reference')->find($id);

        if (!$pagosQuery) {
            abort(404, 'El pago no existe');
        }

        $proyecto = $pagosQuery->proyecto;
        $totalPago = $pagosQuery->valorTotal;

        $descripcion = $pagosQuery->pago_refe
            ->map(function ($ref) {
                $data = $ref->reference?->data_select;
                if (!$data) {
                    return null;
                }
                if (is_array($data) && isset($data[$ref->concepto])) {
                    return $data[$ref->concepto]['msg'];
                }
                return $data['msg'] ?? null;
            })
            ->filter()
            ->implode(', ');

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
            'total_pago'    => $totalPago
        ];

        $pdf = Pdf::loadView('cartera.reciboPDF', $data);
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

    public function sendRC(Request $request){
        $pago = Pagos::find($request->id_pago_rc);
        $pago->update(['rc' => $request->rc]);
        $pago->save();
        return redirect()->back()->with('success', 'Actualizacion exitosa');
    }

}
