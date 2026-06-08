<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use App\Models\Otrosi;
use App\Models\Proyecto;
use App\Models\Pagos;
use App\Models\Documento;
use App\Models\Festivos;
use App\Models\CobroRefe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CarteraController extends Controller
{
    public function index($id_proyecto)
    {
        Gate::authorize('cartera.index');
        $title = 'Cartera abonos del proyecto';
        $proyecto = Proyecto::find($id_proyecto);
        return view('cartera.index', compact( 'title', 'id_proyecto', 'proyecto'));
    }

    public function indexEmpy()
    {
        Gate::authorize('cartera.indexEmpy');
        $title = 'Lista de Cartera';
        $id_proyecto = '';

        $proyectos = Proyecto::whereIn('id_estado', [1, 3, 5])
            ->whereNotNull('cedula_cliente')
            ->whereNotNull('tipo_doc_cliente')
            ->orderBy('nombre_proyecto', 'ASC')
            ->get(['id', 'nombre_proyecto'])
            ->toArray();

        return view('cartera.index', compact( 'title', 'proyectos', 'id_proyecto'));
    }

    public function pagosProyecto($id)
    {
        Gate::authorize('cartera.pagosProyecto');
        return $this->datapagosProyecto($id);
    }

    private function datapagosProyecto($id){
        try {
            $proyecto = Proyecto::with(['otro_si', 'soporteFact'])->findOrFail($id);

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
            $valTotodal = $valProyecto + $valOtrosis;

            $contraProyec = [
                "id_proyecto" => $id,
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
                        "concepto" => "Otrosí N° " . $item->numero,
                        "valor_total" => $item->total_deve,
                        "urlContrato" => route('otro_si.otroSiPdf', $item->id),
                        "pazysalvo" => $item->paz_salvo
                    ];
                })
                ->toArray();
            $contratos = array_merge([$contraProyec], $contraOtrosi);

            $valTotalPagado = Pagos::where('id_proyecto', $id)->get()->sum('valor');

            $pagos = Pagos::with(['proyecto', 'soporte'])
                ->where('id_proyecto', $id)
                ->get()
                ->map(function ($item) {
                    return [
                        "id" => $item->id,
                        "id_proyecto" => $item->id_proyecto,
                        "valor_pago" => $item->valor,
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

            $lineDeveTTOtroSi = [0, 0, 0, 0, 0, 0];
            $flag = 1;
            $lineDeveOtrosi = Otrosi::with('otrosi_refe')
                ->where('id_proyecto', $id)
                ->where('estado', 1)
                ->orderBy('numero', 'asc')
                ->get()
                ->map(function ($item) use (&$lineDeveTTOtroSi, &$flag) {
                    $b = $item->totalDeve;
                    $a = $item->totalRefeOtroSi;
                    $c = $b - $a;
                    if ($c == 0) {
                        $line = [0, 0, 0, 0, 0, 0];
                    } else {
                        $line = [0, -$c, -$c, -$c, -$c, -$c];
                        $flag = 0;
                    }

                    $refe = $item->otrosi_refe()->get();
                    if ($refe->isEmpty()) {
                        $array = array_fill(0, 5, -$b);
                        return array_merge([0], $array);
                    } else {
                        foreach ($refe as $data) {
                            $lineDeveTTOtroSi[$data->referencia] += $data->valor;
                            if ($data->referencia >= 1 && $data->referencia <= 5) {
                                $line[$data->referencia] = $data->valor;
                            }
                        }
                    }
                    return $line;
                })
                ->toArray();

            $lineTTDeve = ['--', '--', '--', '--', '--', '--'];
            if($flag == 1){
                foreach ($lineDeveProy as $key => $valor) {
                    $lineTTDeve[$key] = $valor + (is_numeric($lineDeveTTOtroSi[$key]) ? $lineDeveTTOtroSi[$key] : 0);
                }
            }

            $lineTTResta = ['--', '--', '--', '--', '--', '--'];
            $acumulado = $valTotalPagado;
            $ban = 0;
            $lineCobro = ['--', '--', '--', '--', '--', '--'];
            if($flag == 1){
                foreach ($lineTTDeve as $key => $valor) {
                    $refe = CobroRefe::where('id_proyecto', $id)
                        ->where('referencia', $key)
                        ->first();
                    if($ban == 1){
                        if($flag == 1){
                            $lineTTResta[$key] = $valor;
                            $lineCobro[$key] = $refe ? -1 * $valor : $valor;
                        }
                        continue ;
                    }
                    $acumulado = $acumulado - $valor;
                    if($acumulado > 0){
                        if($flag == 1){
                            $lineTTResta[$key] = 0;
                            $lineCobro[$key] = 0;
                        }
                    } else {
                        $ban = 1;
                        if($flag == 1){
                            $lineTTResta[$key] = abs($acumulado);
                            $lineCobro[$key] = $refe ? -1 * abs($acumulado) : abs($acumulado);
                        }
                    }
                }
            }

            if(empty($lineDeveOtrosi)){
                $valance = array_merge([$lineDeveProy], [$lineTTDeve], [$lineTTResta], [$lineCobro]);
            } else {
                $valance = array_merge([$lineDeveProy], $lineDeveOtrosi, [$lineTTDeve], [$lineTTResta], [$lineCobro]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Consulta exitosa',
                'data' => [
                    "pagos" => $pagos,
                    "total_proyecto" => $valTotodal,
                    "total_pagado" => $valTotalPagado,
                    "resumen" => $contratos,
                    "porcentajes" => $porcentProyec,
                    "valance_pro" => $valance,
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
            'valor' => [ 'required', 'numeric', 'min:1'],
        ];

        $validator = Validator::make($request->all(), $val, [
            'required' => 'Este campo es obligatorio.',
            'integer'  => 'Debe ser un número válido.',
            'numeric'  => 'Debe ser un valor numérico.',
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
            Pagos::create([
                'id_proyecto'  => $request->id_proyecto,
                'fecha_pago'   => $request->fecha_pago,
                'valor'        => $request->valor,
                'comentario'   => $request->comentario ?? '',
                'id_user'      => Auth::id(),
            ]);

            $cobro = CobroRefe::where('id_proyecto', $request->id_proyecto)
                ->where('estado', 1)
                ->where('valor_pendiente', '>', 0)
                ->first();

            if($cobro){
                $cobro->valor_pendiente = max(0, $cobro->valor_pendiente - $request->valor);
                if($cobro->valor_pendiente == 0){
                    $cobro->estado = 2;
                }
                $cobro->fecha_pago_cli = $request->fecha_pago;
                $cobro->save();
            }

            $pro = Proyecto::findOrFail($request->id_proyecto);
            $valOtroSiRefe      = (float) $pro->allTotalRefeOtroSi;
            $valOtrosis         = (float) ($pro->totalOtroSi ?? 0);
            $cambio             = $valOtroSiRefe - $valOtrosis;//valor consfigurado como referecia 0 si consfigurado

            $valProyecto        = (float) $pro->total;
            $porcentajeInicial  = $pro->termino_1_por + $pro->termino_2_por;
            $valProyectoPor     = ($porcentajeInicial * $valProyecto) / 100;
            $valFirstOtroSiRefe = (float) $pro->firstTotalRefeOtroSi;

            // Equivale al 50% del proyecto + referencias iniciales del otrosí
            $valTotal50 = $valProyectoPor + $valFirstOtroSiRefe;

            $valAbonado = (float) Pagos::where('id_proyecto', $pro->id)->sum('valor');//valor total de todos los abonos

            if ($cambio == 0 && $valAbonado >= $valTotal50) {
                if ($pro->id_estado == 2) {
                    $pro->fec_inicio = $request->fecha_pago;
                    $pro->fec_fin_estimado = (new Festivos)
                        ->calcularFechaFin(
                            $request->fecha_pago,
                            $pro->dias_contrato
                        );
                    $diasComision = $pro->dias_contrato - 10;
                    if ($diasComision > 0) {
                        $pro->fecha_comision = (new Festivos)
                            ->calcularFechaFin(
                                $request->fecha_pago,
                                $diasComision
                            );
                    }
                    $pro->id_estado = 1;
                }
                if(($valProyecto + $valOtrosis) <= $valAbonado){
                    $pro->paz_salvo = 1;
                } else{
                    $pro->paz_salvo = 0;
                }
                $pro->save();
            }
            DB::commit();
            return response()->json([
                'status'  => true,
                'url' => route('cartera.index', $request->id_proyecto),
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
        $pagosQuery = Pagos::with('proyecto')->find($id);

        if (!$pagosQuery) {
            abort(404, 'El pago no existe');
        }

        $proyecto = $pagosQuery->proyecto;
        $totalPago = $pagosQuery->valor;

        $pagosQuery->descripcion = 'Abono del Proyecto';

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

        DB::beginTransaction();
        try {
            $pago = Pagos::findOrFail($id);
            $pago->delete();

            $pro = Proyecto::findOrFail($pago->id_proyecto);
            $valProyecto        = (float) $pro->total;
            $valOtrosis         = (float) ($pro->totalOtroSi ?? 0);

            $valAbonado = (float) Pagos::where('id_proyecto', $pro->id)->sum('valor');

            if(($valProyecto + $valOtrosis) <= $valAbonado){
                $pro->paz_salvo = 1;
            } else{
                $pro->paz_salvo = 0;
            }
            $pro->save();

            DB::commit();
            return response()->json([
                'status'  => true,
                'message' => 'Pago eliminado correctamente.',
                'url' => route('cartera.index', $pago->id_proyecto),
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

    public function selectCobro(Request $request){

        $validator = Validator::make($request->all(), [
            'id_proyecto' => 'required',
            'valor'       => 'required|numeric',
            'referencia'  => 'required' 
        ]);

        // Si la validación falla, respondemos con 422 JSON (No causará un 302)
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $cobro = new CobroRefe();
            $cobro->id_proyecto    = $request->input('id_proyecto');
            $cobro->referencia     = $request->input('referencia'); 
            $cobro->valor_pendiente= $request->input('valor');
            $cobro->id_user        = Auth::id(); 
            $cobro->save();

            return response()->json([
                'success' => true,
                'message' => 'El cobro con referencia se ha guardado exitosamente.',
                'data'    => $cobro
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al guardar el cobro: ' . $e->getMessage()
            ], 500);
        }
    }

}
