<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\PlanillaEntregables;
use App\Models\Otrosi;
use App\Models\ConfigPorcentajes;
use App\Models\ValorArea;
use App\Models\ValorAreaEnchape;
use App\Models\PlanillaValorProyecto;

use App\Http\Requests\SavePlantillaRequest;
use App\Http\Requests\SaveConfigPlantillaRequest;
use App\Http\Requests\SaveCobrosPlanillaRequest;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PlanillaController extends Controller
{
    public function index($id)
    {
        Gate::authorize('planilla.index');
        $proyecto = Proyecto::findOrFail($id);
        $this->autorizarProyecto($proyecto);

        $departamentos = json_decode(file_get_contents(storage_path('json/jsonCityColombia.json')), true);
        $ubicacion = Proyecto::$ubicacion;

        $planillasDb = PlanillaEntregables::where('id_proyecto', $id)->get();
        
        $planillaEntregables = $planillasDb->whereIn('tipo', [1,2,3,4])->map(function ($row) {
            return [
                'id_area'  => (int) $row->tipo,
                'areaText' => PlanillaEntregables::$txTipo[$row->tipo] ?? '',
                'items'    => collect($row->items ?? [])->map(function ($item) {
                    return [
                        'id'              => $item['id'] ?? null,
                        'material'        => $item['descripccion'] ?? '',
                        'cantidad'        => (float) ($item['cantidad'] ?? 0),
                        'valor_unitario'  => (int) ($item['valor_uni'] ?? 0),
                        'unidad'          => (int) ($item['unidad'] ?? 1),
                        'porcentage'      => (int) ($item['porcentage'] ?? 0),
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        $entregableTipo5 = $planillasDb->where('tipo', 5)->first();
        $otroSi = $proyecto->otro_si()->where('estado', 1)->get();
        $valOtrosis = $proyecto->TotalOtroSi ?? 0;

        $tipos = collect(PlanillaEntregables::$txTipo)->except(5)->toArray();
        $unidades = Otrosi::$unidades;
        $areaProyecto = $proyecto->area_privada;

        $añoMax = ConfigPorcentajes::max('año');
        $dataConfigPorcentajes = ConfigPorcentajes::where('año', $añoMax)->get();

        $dataValorArea = ValorArea::where('area_min', '<=', $areaProyecto)->where('area_max', '>=', $areaProyecto)->latest('año')->first();
        $dataValorAreaEnchape = ValorAreaEnchape::where('area_min', '<=', $areaProyecto)->where('area_max', '>=', $areaProyecto)->latest('año')->first();

        $valoresProyecto = PlanillaValorProyecto::where('id_proyecto', $id)->get()->keyBy('tipo');
        $configProyecto = $valoresProyecto;

        $cobros = $this->armarCobros($valoresProyecto->get(1), $planillasDb);

        $title = 'Planilla del Proyecto';

        return view('planilla.planillaProyecto',
            compact('proyecto', 'departamentos', 'ubicacion', 'title', 'planillaEntregables', 'otroSi', 'tipos', 'unidades',
            'configProyecto', 'dataValorArea', 'dataValorAreaEnchape', 'dataConfigPorcentajes', 'valOtrosis', 'cobros',
            'valoresProyecto', 'entregableTipo5'));
    }

    public function savePlantilla(SavePlantillaRequest $request)
    {
        Gate::authorize('planilla.savePlantilla');
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $userId     = Auth::id();
            $idProyecto = $data['id_proyecto'];

            $itemsPorTipo = [];
            if (!empty($data['entregables'])) {
                foreach ($data['entregables'] as $entregable) {
                    $tipo = (int) $entregable['id_area'];
                    $itemsPorTipo[$tipo][] = [
                        'unidad'       => (int) $entregable['unidad'],
                        'cantidad'     => (float) $entregable['cantidad'],
                        'valor_uni'    => (int) $entregable['valor_unitario'],
                        'descripccion' => $entregable['material'],
                        'porcentage'   => (int) ($entregable['porcentage'] ?? 0),
                    ];
                }
            }

            PlanillaEntregables::where('id_proyecto', $idProyecto)
                ->whereIn('tipo', [1, 2, 3, 4])
                ->whereNotIn('tipo', array_keys($itemsPorTipo) ?: [0])
                ->delete();

            foreach ($itemsPorTipo as $tipo => $items) {
                PlanillaEntregables::updateOrCreate(
                    ['id_proyecto' => $idProyecto, 'tipo' => $tipo],
                    ['id_user' => $userId, 'items' => $items]
                );
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Entregables guardados.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function saveConfigPlantilla(SaveConfigPlantillaRequest $request)
    {
        Gate::authorize('planilla.saveConfigPlantilla');
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $userId = Auth::id();

            if ((int) $data['tipo'] === 3) {
                PlanillaEntregables::updateOrCreate(
                    ['id_proyecto' => $data['id_proyecto'], 'tipo' => 5],
                    ['id_user' => $userId, 'items' => $data['conceptos']]
                );
            } else {
                PlanillaValorProyecto::updateOrCreate(
                    ['id_proyecto' => $data['id_proyecto'], 'tipo' => $data['tipo']],
                    ['id_user' => $userId, 'valor' => (int) $data['valor_config']]
                );
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Configuración guardada.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteConfigPlantilla($idProyecto, $tipo)
    {
        Gate::authorize('planilla.deleteConfigPlantilla');

        DB::beginTransaction();
        try {
            if ((int) $tipo === 3) {
                PlanillaEntregables::where('id_proyecto', $idProyecto)->where('tipo', 5)->delete();
            } else {
                PlanillaValorProyecto::where('id_proyecto', $idProyecto)->where('tipo', $tipo)->delete();
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Configuración eliminada.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function saveCobros(SaveCobrosPlanillaRequest $request)
    {
        Gate::authorize('planilla.saveCobros');
        $data = $request->validated();

        $proyecto = Proyecto::findOrFail($data['id_proyecto']);
        $this->autorizarProyecto($proyecto);

        $user   = Auth::user();
        $userId = (int) $user->id;
        $puede  = $this->permisosCobroPorRol($user);
        $ahora  = now()->toDateTimeString();

        DB::beginTransaction();
        try {
            $cfg1 = PlanillaValorProyecto::where('id_proyecto', $proyecto->id)->where('tipo', 1)->lockForUpdate()->first();
            // Quitamos la excepción para que no bloquee
            $valorBase = $cfg1 ? (float) $cfg1->valor : 0; 

            foreach ($data['cobros'] as $tipoFila => $itemsCobros) {
                $planilla = PlanillaEntregables::where('id_proyecto', $proyecto->id)->where('tipo', $tipoFila)->lockForUpdate()->first();
                if (!$planilla) continue;

                $itemsGuardados = $planilla->items;
                $esTipo5 = ((int)$tipoFila === 5);

                foreach ($itemsCobros as $idx => $in) {
                    $idx = (int) $idx;
                    if (!isset($itemsGuardados[$idx])) continue;

                    $itemBase = $itemsGuardados[$idx];
                    $nombreItem = $esTipo5 ? ($itemBase['concepto'] ?? ($idx + 1)) : ($itemBase['descripccion'] ?? ($idx + 1));
                    $etq = 'Ítem "' . $nombreItem . '"';
                    
                    $tope = $esTipo5 
                        ? PlanillaEntregables::montoItem($itemBase, $valorBase)
                        : (int)round(($itemBase['cantidad'] ?? 0) * ($itemBase['valor_uni'] ?? 0));

                    $c = PlanillaEntregables::normalizarCobro($itemBase);

                    $revierte = array_key_exists('aprobacion', $in) && $in['aprobacion'] !== null && (int) $in['aprobacion'] === 0 && $c['aprobacion'] > 0;

                    if ($revierte) {
                        $this->aplicarAprobacion($c, $in, $etq, $puede['aprob'], $userId, $ahora, $tope);
                        $this->aplicarValor($c, $in, $tope, $etq, $puede['valor'], $userId);
                    } else {
                        $this->aplicarValor($c, $in, $tope, $etq, $puede['valor'], $userId);
                        $this->aplicarAprobacion($c, $in, $etq, $puede['aprob'], $userId, $ahora, $tope);
                    }
                    $this->aplicarFecha($c, $in, $etq, $puede['fecha'], $userId);

                    $itemsGuardados[$idx] = array_merge($itemsGuardados[$idx], $c);
                }
                $planilla->items = array_values($itemsGuardados);
                $planilla->save();
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Cobros guardados.']);
        } catch (\DomainException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function aplicarValor(array &$c, array $in, int $tope, string $etq, bool $rolPuede, int $userId): void
    {
        if (!array_key_exists('valor_cobrar', $in)) return;

        $nuevo = ($in['valor_cobrar'] === null || $in['valor_cobrar'] === '' || (float) $in['valor_cobrar'] <= 0)
            ? null : (int) round((float) $in['valor_cobrar']);

        if ($nuevo === $c['valor_cobrar']) return;

        if (!$rolPuede || $c['fecha_pago'] !== null || $c['aprobacion'] > 0) {
            throw new \DomainException("$etq: el valor a cobrar ya no se puede modificar. Recargue la página.");
        }

        if ($nuevo !== null && $nuevo > $tope) {
            throw new \DomainException("$etq: el valor a cobrar no puede superar $ " . number_format($tope, 0, ',', '.') . '.');
        }

        $c['valor_cobrar']     = $nuevo;
        $c['valor_cobrar_por'] = $userId;
    }

    private function aplicarAprobacion(array &$c, array $in, string $etq, bool $rolPuede, int $userId, string $ahora, int $tope): void
    {
        if (!array_key_exists('aprobacion', $in) || $in['aprobacion'] === null) return;

        $nuevo = (int) $in['aprobacion'];

        if ($nuevo === $c['aprobacion']) return;

        if (!$rolPuede || $c['fecha_pago'] !== null) {
            throw new \DomainException("$etq: la aprobación ya no se puede modificar. Recargue la página.");
        }

        if ($nuevo > 0 && $c['valor_cobrar'] === null) {
            throw new \DomainException("$etq: aún no tiene valor a cobrar registrado.");
        }

        if ($nuevo === 50 && $c['valor_cobrar'] == ($tope / 2)) {
            throw new \DomainException("$etq: si el valor a cobrar es el 50%, la aprobación debe ser Sin aprobar (0) o 100%.");
        }

        $c['aprobacion']     = $nuevo;
        $c['aprobacion_por'] = $userId;
        $c['aprobacion_at']  = $ahora;
    }

    private function aplicarFecha(array &$c, array $in, string $etq, bool $rolPuede, int $userId): void
    {
        if (!array_key_exists('fecha_pago', $in)) return;

        $nueva = !empty($in['fecha_pago']) ? $in['fecha_pago'] : null;

        if ($nueva === $c['fecha_pago']) return;

        if (!$rolPuede) {
            throw new \DomainException("$etq: la fecha de pago no se puede modificar.");
        }

        if ($nueva !== null && ($c['aprobacion'] <= 0 || $c['valor_cobrar'] === null)) {
            throw new \DomainException("$etq: la fecha de pago requiere valor a cobrar y aprobación (50% o 100%).");
        }

        $c['fecha_pago']     = $nueva;
        $c['fecha_pago_por'] = $userId;
    }

    private function permisosCobroPorRol($user): array
    {
        return [
            'valor' => (bool) ($user->isAdmin || $user->isContratista),
            'aprob' => (bool) ($user->isAdmin || $user->isColab),
            'fecha' => (bool) $user->isAdmin,
        ];
    }

    private function autorizarProyecto(Proyecto $proyecto): void
    {
        $user = Auth::user();

        if ($user->isAdmin) return;

        $permitido = ($user->isColab && (int) $proyecto->id_user === (int) $user->id)
            || ($user->isContratista && (int) $proyecto->id_user_obra_blanca === (int) $user->id);

        abort_unless($permitido, 403, 'No tiene acceso a este proyecto.');
    }

    private function armarCobros($valorProyecto, $planillasDb): array
    {
        $resultado = [
            'activo'       => true, // Forzamos a true para que la tabla siempre se muestre
            'hay'          => false,
            'puedeGuardar' => false,
            'entregables'  => [],
            'tipo5'        => [],
            'totales'      => ['cobrado' => 0, 'aprobado' => 0, 'pagado' => 0, 'por_pagar' => 0, 'n_aprobados' => 0],
        ];

        $puede     = $this->permisosCobroPorRol(Auth::user());
        $valorBase = $valorProyecto ? (float) $valorProyecto->valor : 0; 

        foreach ($planillasDb as $planilla) {
            $tipo = $planilla->tipo;
            $esTipo5 = ($tipo == 5);
            $bloqueadoGrupo = false;

            foreach ($planilla->items as $item) {
                $c = PlanillaEntregables::normalizarCobro($item);
                if ($c['valor_cobrar'] !== null || $c['aprobacion'] > 0 || $c['fecha_pago'] !== null) {
                    $bloqueadoGrupo = true;
                    break;
                }
            }

            foreach ($planilla->items as $idx => $item) {
                $tope = $esTipo5 ? PlanillaEntregables::montoItem($item, $valorBase) : (int)round(($item['cantidad'] ?? 0) * ($item['valor_uni'] ?? 0));
                $c = PlanillaEntregables::normalizarCobro($item);
                
                $bloqueado  = $c['fecha_pago'] !== null;
                $tieneValor = $c['valor_cobrar'] !== null;

                $canValor = $puede['valor'] && !$bloqueado && $c['aprobacion'] === 0;
                $canAprob = $puede['aprob'] && !$bloqueado && $tieneValor;
                $canFecha = $puede['fecha'] && $tieneValor && $c['aprobacion'] > 0;

                $aprobado = PlanillaEntregables::valorAprobado($c['valor_cobrar'], $c['aprobacion']);
                $estado = $bloqueado ? 'pagado' : ($c['aprobacion'] > 0 ? 'aprobado' : ($tieneValor ? 'cobrado' : 'pendiente'));

                if ($tieneValor) { $resultado['hay'] = true; $resultado['totales']['cobrado'] += $c['valor_cobrar']; }
                if ($c['aprobacion'] > 0) {
                    $resultado['totales']['n_aprobados']++;
                    $resultado['totales']['aprobado'] += $aprobado;
                    if ($bloqueado) $resultado['totales']['pagado'] += $aprobado;
                    else $resultado['totales']['por_pagar'] += $aprobado;
                }
                if ($canValor || $canAprob || $canFecha) $resultado['puedeGuardar'] = true;

                $datosItem = [
                    'tipo'           => $tipo,
                    'idx'            => $idx,
                    'concepto'       => $esTipo5 ? ($item['concepto'] ?? 'Sin concepto') : ($item['descripccion'] ?? ''),
                    'en_pesos'       => $esTipo5 && (int)($item['en_pesos'] ?? 0) === 1,
                    'cantidad'       => $esTipo5 ? 0 : (float)($item['cantidad'] ?? 0),
                    'unidad'         => $esTipo5 ? 0 : (int)($item['unidad'] ?? 1),
                    'valor_uni'      => $esTipo5 ? 0 : (int)($item['valor_uni'] ?? 0),
                    'porcentage'     => (float)($item['porcentage'] ?? 0), // Ahora aplica para todos
                    'tope'           => $tope,
                    'valor_cobrar'   => $c['valor_cobrar'],
                    'esMitad'        => $tieneValor && ($c['valor_cobrar'] == ($tope / 2)),
                    'aprobacion'     => $c['aprobacion'],
                    'fecha_pago'     => $c['fecha_pago'],
                    'valor_aprobado' => $aprobado,
                    'estado'         => $estado,
                    'canValor'       => $canValor,
                    'canAprob'       => $canAprob,
                    'canFecha'       => $canFecha,
                    'puedeEditar'    => !$bloqueadoGrupo,
                ];

                if ($esTipo5) $resultado['tipo5'][] = $datosItem;
                else $resultado['entregables'][$tipo][] = $datosItem;
            }
        }
        return $resultado;
    }

    public function pdfConfigPlanilla($tipo, $idProyecto)
    {
        Gate::authorize('planilla.pdfConfigPlanilla');

        $proyecto = Proyecto::findOrFail($idProyecto);
        $valoresProyecto = PlanillaValorProyecto::where('id_proyecto', $idProyecto)->get()->keyBy('tipo');
        $entregableTipo5 = PlanillaEntregables::where('id_proyecto', $idProyecto)->where('tipo', 5)->first();

        $valorBase = 0;
        if ($tipo == 1 && isset($valoresProyecto[1])) $valorBase = $valoresProyecto[1]->valor;
        elseif ($tipo == 2 && isset($valoresProyecto[2])) $valorBase = $valoresProyecto[2]->valor;

        $porcentajes = [];
        $totalDesglose = 0;
        $txTipo = PlanillaValorProyecto::$txTipo[$tipo] ?? 'Configuración General';

        if ($entregableTipo5) {
            foreach ($entregableTipo5->items as $item) {
                $porcentaje = (float) ($item['porcentage'] ?? 0);
                $montoCalculado = ((int) ($item['en_pesos'] ?? 0) === 0) ? $valorBase * ($porcentaje / 100) : $porcentaje;

                $porcentajes[] = [
                    'concepto'   => $item['concepto'] ?? 'Sin concepto',
                    'porcentaje' => $porcentaje,
                    'monto'      => $montoCalculado,
                    'en_pesos'   => $item['en_pesos'] ?? 0,
                ];
                $totalDesglose += $montoCalculado;
            }
        }

        $dataEntrePlanilla = PlanillaEntregables::where('id_proyecto', $idProyecto)->whereIn('tipo', [1, 2, 3, 4])->get();
        $txTipoEntre = PlanillaEntregables::$txTipo;
        $arr = [];
        $valorTotal = 0;

        foreach ($dataEntrePlanilla as $row) {
            $area = $txTipoEntre[$row->tipo] ?? '';
            if (!isset($arr[$area])) $arr[$area] = ["espacio" => $area, "items" => [], "subtotal" => 0];

            foreach ($row->items ?? [] as $item) {
                $val = (int) ($item['valor_uni'] ?? 0) * (float) ($item['cantidad'] ?? 0);
                $arr[$area]["items"][] = [
                    "descripcion"    => $item['descripccion'] ?? '',
                    "cantidad"       => $item['cantidad'] ?? 0,
                    "valor_unitario" => number_format((int) ($item['valor_uni'] ?? 0), 0, ',', '.'),
                    "valor_total"    => number_format($val, 0, ',', '.'),
                    "unidad"         => $item['unidad'] ?? 1,
                ];
                $arr[$area]["subtotal"] += $val;
                $valorTotal += $val;
            }
        }
        $adicionales = $arr;
        $unidades = Otrosi::$unidades;

        $pdf = Pdf::loadView('planilla.pdfPlanillaConfig',
            compact('proyecto', 'valorBase', 'porcentajes', 'totalDesglose', 'tipo', 'txTipo',
            'adicionales', 'txTipoEntre', 'unidades', 'valorTotal'));

        return $pdf->stream('configuracion-planilla-proyecto-' . $proyecto->id . '.pdf');
    }
}