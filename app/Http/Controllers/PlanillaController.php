<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\PlanillaEntregables;
use App\Models\OtroSi;

use App\Models\ConfigPorcentajes;
use App\Models\ValorArea;
use App\Models\ValorAreaEnchape;
use App\Models\PlanillaConfigProyecto;

use App\Http\Requests\SavePlantillaRequest;
use App\Http\Requests\SaveConfigPlantillaRequest;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


class PlanillaController extends Controller
{

    public function index($id)
    {
        Gate::authorize('proyecto.index');
        $proyecto = Proyecto::findOrFail($id);
        $departamentos = json_decode(file_get_contents(storage_path('json/jsonCityColombia.json')), true);

        $planillaEntregables = PlanillaEntregables::where('id_proyecto', $id)
            ->get()
            ->groupBy('tipo')
            ->map(function ($items, $tipo) {
                return [
                    'id_area'  => (int) $tipo,
                    'areaText' => PlanillaEntregables::$txTipo[$tipo] ?? '',
                    'items'    => $items->map(function ($item) {

                        return [
                            'id'              => $item->id,
                            'material'        => $item->descripccion,
                            'cantidad'        => (float) $item->cantidad,
                            'valor_unitario'  => (int) $item->valor_uni,
                            'unidad'          => (int) $item->unidad,
                        ];

                    })->values()->toArray(),
                ];

            })
            ->values()
            ->toArray();

        $otroSi = $proyecto->otro_si()->where('estado', 1)->get();

        $valOtrosis = $proyecto->TotalOtroSi?? 0;

        $tipos = PlanillaEntregables::$txTipo;
        $unidades = OtroSi::$unidades;

        $areaProyecto = $proyecto->area_privada;

        $añoMax = ConfigPorcentajes::max('año');
        $dataConfigPorcentajes = ConfigPorcentajes::where('año', $añoMax)->get();

        $dataValorArea = ValorArea::where('area_min', '<=', $areaProyecto)
            ->where('area_max', '>=', $areaProyecto)
            ->latest('año')
            ->first();

        $dataValorAreaEnchape = ValorAreaEnchape::where('area_min', '<=', $areaProyecto)
            ->where('area_max', '>=', $areaProyecto)
            ->latest('año')
            ->first();

        $configProyecto = PlanillaConfigProyecto::where('id_proyecto', $id)->get()->keyBy('tipo');

        $title = 'Planilla del Proyecto';

        return view('planilla.planillaProyecto', 
            compact('proyecto', 'departamentos', 'title', 'planillaEntregables', 'otroSi', 'tipos', 'unidades', 
            'configProyecto', 'dataValorArea', 'dataValorAreaEnchape', 'dataConfigPorcentajes', 'valOtrosis'));
    }

    public function savePlantilla(SavePlantillaRequest $request)
    {
        Gate::authorize('planilla.savePlantilla');
        $data = $request->validated();

        DB::beginTransaction();
        try {

            $idsRecibidos = [];
            foreach ($data['entregables'] as $entregable) {
                if (!empty($entregable['id'])) {
                    $idsRecibidos[] = $entregable['id'];
                }
            }

            // Eliminar los registros que ya no existen
            $query = PlanillaEntregables::where('id_proyecto', $data['id_proyecto']);
            if (!empty($idsRecibidos)) {
                $query->whereNotIn('id', $idsRecibidos);
            }
            $query->delete();

            $userId = Auth::id();

            // Crear o actualizar
            foreach ($data['entregables'] as $entregable) {
                PlanillaEntregables::updateOrCreate(
                    [
                        'id' => $entregable['id'] ?? null,
                    ],
                    [
                        'id_proyecto'  => $data['id_proyecto'],
                        'id_area'      => $entregable['id_area'],
                        'id_user'      => $userId,
                        'tipo'         => $entregable['id_area'],
                        'unidad'       => $entregable['unidad'],
                        'cantidad'     => $entregable['cantidad'],
                        'valor_uni'    => $entregable['valor_unitario'],
                        'descripccion' => $entregable['material'],
                    ]
                );
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Entregables guardados correctamente.'
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar los Entregables.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function saveConfigPlantilla(SaveConfigPlantillaRequest $request)
    {
        Gate::authorize('planilla.saveConfigPlantilla');
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $userId = Auth::id();

            // tipo 1/2 -> número plano | tipo 3 -> JSON de conceptos
            $valor = (int) $data['tipo'] === 3
                ? json_encode($data['conceptos'])
                : $data['valor_config'];

            PlanillaConfigProyecto::updateOrCreate(
                [
                    'id_proyecto' => $data['id_proyecto'],
                    'tipo'        => $data['tipo'],
                ],
                [
                    'id_user'      => $userId,
                    'valor_config' => $valor,
                ]
            );

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Configuración guardada correctamente.'
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la configuración.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function deleteConfigPlantilla($idProyecto, $tipo)
    {
        Gate::authorize('planilla.deleteConfigPlantilla');

        DB::beginTransaction();
        try {
            PlanillaConfigProyecto::where('id_proyecto', $idProyecto)
                ->where('tipo', $tipo)
                ->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Configuración eliminada correctamente.'
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la configuración.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function pdfConfigPlanilla($tipo, $idProyecto)
    {
        $proyecto = Proyecto::findOrFail($idProyecto);
        $configProyecto = PlanillaConfigProyecto::where('id_proyecto', $idProyecto)->get()->keyBy('tipo');

        // 1. Obtener el valor base del Tipo 1 o Tipo 2
        $valorBase = 0;
        if ($tipo == 1 && isset($configProyecto[1])) {
            $valorBase = $configProyecto[1]->valor;
        } elseif ($tipo == 2 && isset($configProyecto[2])) {
            $valorBase = $configProyecto[2]->valor;
        }

        // 2. Calcular los porcentajes del Tipo 3 aplicados al valor base
        $porcentajes = [];
        $totalDesglose = 0;
        
        $txTipo = PlanillaConfigProyecto::$txTipo[$tipo] ?? 'Configuración General';

        if (isset($configProyecto[3])) {
            $itemsPorcentaje = is_string($configProyecto[3]->valor) 
                ? json_decode($configProyecto[3]->valor, true) 
                : $configProyecto[3]->valor;

            foreach ($itemsPorcentaje as $item) {
                $porcentaje = (float)($item['porcentage'] ?? 0);

                if(isset($item['en_pesos']) && $item['en_pesos'] == 0) {
                    $montoCalculado = $valorBase * ($porcentaje / 100);
                } else {
                    $montoCalculado = $porcentaje;
                }
                
                $porcentajes[] = [
                    'concepto' => $item['concepto'] ?? 'Sin concepto',
                    'porcentaje' => $porcentaje,
                    'monto' => $montoCalculado,
                    'en_pesos' => $item['en_pesos'] ?? 0,
                ];

                $totalDesglose += $montoCalculado;
            }
        }

        $dataEntrePlanilla = PlanillaEntregables::where('id_proyecto', $idProyecto)->get();
        $txTipoEntre = PlanillaEntregables::$txTipo;
        $arr = [];
        $valorTotal = 0;
        foreach ($dataEntrePlanilla as $item) {
            $area = $txTipoEntre[$item->tipo];
            if (!isset($arr[$area])) {
                $arr[$area] = [
                    "espacio"       => $area,
                    "items"         => [],
                    "subtotal"      => 0
                ];
            }

            $val = $item->valor_uni * $item->cantidad;

            $arr[$area]["items"][] = [
                "descripcion"   => $item->descripccion,
                "cantidad"      => $item->cantidad,
                "valor_unitario"=> number_format($item->valor_uni, 0, ',', '.'),
                "valor_total"   => number_format($val, 0, ',', '.'),
                "unidad"      => $item->unidad,
            ];
            $arr[$area]["subtotal"] += $val;
            $valorTotal += $val;
        }
        $adicionales = $arr;

        $unidades = OtroSi::$unidades;

        // 3. Cargar la vista y generar el PDF
        $pdf = Pdf::loadView('planilla.pdfPlanillaConfig', 
            compact('proyecto', 'valorBase', 'porcentajes', 'totalDesglose', 'tipo', 'txTipo', 
            'adicionales', 'txTipoEntre', 'unidades', 'valorTotal'));

        // Opcional: usar ->download('nombre.pdf') si prefieres descarga directa en lugar de visualización en pestaña (`->stream()`)
        return $pdf->stream('configuracion-planilla-proyecto-' . $proyecto->id . '.pdf');
    }

}
