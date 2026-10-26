<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\PlanillaEntregables;
use App\Models\Otrosi;

use App\Models\ConfigPorcentajes;
use App\Models\ValorArea;
use App\Models\ValorAreaEnchape;
use App\Models\PlanillaValorProyecto;   // CAMBIO: reemplaza a PlanillaConfigProyecto

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

        // CAMBIO: ahora una fila por tipo (tipos 1..4), con items JSON dentro.
        // Se mapea al shape que espera create.js (material / valor_unitario).
        $planillaEntregables = PlanillaEntregables::where('id_proyecto', $id)
            ->whereIn('tipo', [1, 2, 3, 4])
            ->get()
            ->map(function ($row) {
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
            })
            ->values()
            ->toArray();

        // CAMBIO: fila tipo 5 (Porcentajes del Proyecto) — antes vivía en planilla_confi_proyecto.tipo=3
        $entregableTipo5 = PlanillaEntregables::where('id_proyecto', $id)
            ->where('tipo', 5)
            ->first();

        $otroSi = $proyecto->otro_si()->where('estado', 1)->get();

        $valOtrosis = $proyecto->TotalOtroSi ?? 0;

        // CAMBIO: el modal solo muestra tipos 1..4 (excluye el 5)
        $tipos = collect(PlanillaEntregables::$txTipo)->except(5)->toArray();
        $unidades = Otrosi::$unidades;

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

        // CAMBIO: antes PlanillaConfigProyecto → ahora PlanillaValorProyecto (tipos 1,2,4,5,6)
        $valoresProyecto = PlanillaValorProyecto::where('id_proyecto', $id)->get()->keyBy('tipo');

        // CAMBIO TEMPORAL: alias para no romper el blade actual. Se quita en el Paso 5.
        $configProyecto = $valoresProyecto;

        // CAMBIO: armarCobros recibe ahora las dos fuentes por separado
        $cobros = $this->armarCobros($valoresProyecto->get(1), $entregableTipo5);

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

            // CAMBIO: agrupar items por tipo (una fila por tipo, JSON con el array)
            $itemsPorTipo = [];
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

            // CAMBIO: borrar filas 1..4 no recibidas (nunca toca el tipo 5)
            PlanillaEntregables::where('id_proyecto', $idProyecto)
                ->whereIn('tipo', [1, 2, 3, 4])
                ->whereNotIn('tipo', array_keys($itemsPorTipo) ?: [0])
                ->delete();

            // CAMBIO: updateOrCreate por (id_proyecto, tipo) con items = array JSON
            foreach ($itemsPorTipo as $tipo => $items) {
                PlanillaEntregables::updateOrCreate(
                    [
                        'id_proyecto' => $idProyecto,
                        'tipo'        => $tipo,
                    ],
                    [
                        'id_user' => $userId,
                        'items'   => $items,
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

            // CAMBIO: tipo 3 (front) → PlanillaEntregables.tipo=5 (Porcentajes)
            //         tipos 1,2,4,5,6 → PlanillaValorProyecto (número)
            if ((int) $data['tipo'] === 3) {
                PlanillaEntregables::updateOrCreate(
                    [
                        'id_proyecto' => $data['id_proyecto'],
                        'tipo'        => 5,
                    ],
                    [
                        'id_user' => $userId,
                        'items'   => $data['conceptos'],   // cast array lo serializa
                    ]
                );
            } else {
                PlanillaValorProyecto::updateOrCreate(
                    [
                        'id_proyecto' => $data['id_proyecto'],
                        'tipo'        => $data['tipo'],
                    ],
                    [
                        'id_user' => $userId,
                        'valor'   => (int) $data['valor_config'],
                    ]
                );
            }

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
            // CAMBIO: tipo 3 (front) → borra PlanillaEntregables.tipo=5
            //         tipos 1,2,4,5,6 → borra PlanillaValorProyecto
            if ((int) $tipo === 3) {
                PlanillaEntregables::where('id_proyecto', $idProyecto)
                    ->where('tipo', 5)
                    ->delete();
            } else {
                PlanillaValorProyecto::where('id_proyecto', $idProyecto)
                    ->where('tipo', $tipo)
                    ->delete();
            }

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
            // CAMBIO: cfg1 viene de PlanillaValorProyecto, cfg3 de PlanillaEntregables.tipo=5
            $cfg1 = PlanillaValorProyecto::where('id_proyecto', $proyecto->id)->where('tipo', 1)->lockForUpdate()->first();
            $cfg3 = PlanillaEntregables::where('id_proyecto', $proyecto->id)->where('tipo', 5)->lockForUpdate()->first();

            if (!$cfg1 || !$cfg3) {
                throw new \DomainException('Debe existir la configuración aceptada de Presupuesto por Proyecto y Porcentajes.');
            }

            $valorBase = (float) $cfg1->valor;
            $items     = $cfg3->items; // cast array: ya es arreglo

            foreach ($data['cobros'] as $idx => $in) {
                $idx = (int) $idx;

                if (!isset($items[$idx]) || !is_array($items[$idx])) {
                    throw new \DomainException('Uno de los ítems enviados no existe. Recargue la página e intente de nuevo.');
                }

                $etq  = 'Ítem "' . ($items[$idx]['concepto'] ?? ($idx + 1)) . '"';
                // CAMBIO: helpers mudados a PlanillaEntregables
                $tope = PlanillaEntregables::montoItem($items[$idx], $valorBase);
                $c    = PlanillaEntregables::normalizarCobro($items[$idx]);

                $revierte = array_key_exists('aprobacion', $in)
                    && $in['aprobacion'] !== null
                    && (int) $in['aprobacion'] === 0
                    && $c['aprobacion'] > 0;

                if ($revierte) {
                    $this->aplicarAprobacion($c, $in, $etq, $puede['aprob'], $userId, $ahora, $tope);
                    $this->aplicarValor($c, $in, $tope, $etq, $puede['valor'], $userId);
                } else {
                    $this->aplicarValor($c, $in, $tope, $etq, $puede['valor'], $userId);
                    $this->aplicarAprobacion($c, $in, $etq, $puede['aprob'], $userId, $ahora, $tope);
                }
                $this->aplicarFecha($c, $in, $etq, $puede['fecha'], $userId);

                $items[$idx] = array_merge($items[$idx], $c);
            }

            // CAMBIO: cast array → asignación directa (Laravel serializa)
            $cfg3->items = array_values($items);
            $cfg3->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Cobros guardados correctamente.'
            ], 200);
        } catch (\DomainException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar los cobros.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    private function aplicarValor(array &$c, array $in, int $tope, string $etq, bool $rolPuede, int $userId): void
    {
        if (!array_key_exists('valor_cobrar', $in)) {
            return;
        }

        $nuevo = ($in['valor_cobrar'] === null || $in['valor_cobrar'] === '' || (float) $in['valor_cobrar'] <= 0)
            ? null
            : (int) round((float) $in['valor_cobrar']);

        if ($nuevo === $c['valor_cobrar']) {
            return;
        }

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
        if (!array_key_exists('aprobacion', $in) || $in['aprobacion'] === null) {
            return;
        }

        $nuevo = (int) $in['aprobacion'];

        if ($nuevo === $c['aprobacion']) {
            return;
        }

        if (!$rolPuede || $c['fecha_pago'] !== null) {
            throw new \DomainException("$etq: la aprobación ya no se puede modificar. Recargue la página.");
        }

        if ($nuevo > 0 && $c['valor_cobrar'] === null) {
            throw new \DomainException("$etq: aún no tiene valor a cobrar registrado.");
        }

        // NUEVO: Validación del 50% sobre 50%
        if ($nuevo === 50 && $c['valor_cobrar'] == ($tope / 2)) {
            throw new \DomainException("$etq: si el valor a cobrar es el 50%, la aprobación debe ser Sin aprobar (0) o 100%.");
        }

        $c['aprobacion']     = $nuevo;
        $c['aprobacion_por'] = $userId;
        $c['aprobacion_at']  = $ahora;
    }

    private function aplicarFecha(array &$c, array $in, string $etq, bool $rolPuede, int $userId): void
    {
        if (!array_key_exists('fecha_pago', $in)) {
            return;
        }

        // Si viene vacío o nulo, lo convertimos a null
        $nueva = !empty($in['fecha_pago']) ? $in['fecha_pago'] : null;

        if ($nueva === $c['fecha_pago']) {
            return;
        }

        // CAMBIO: Solo bloqueamos si el rol no puede. 
        // Si es Admin ($rolPuede = true), puede cambiarla o borrarla.
        if (!$rolPuede) {
            throw new \DomainException("$etq: la fecha de pago no se puede modificar.");
        }

        // Si está asignando una fecha (no la está borrando), validar que tenga aprobación y valor
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

        if ($user->isAdmin) {
            return;
        }

        $permitido = ($user->isColab && (int) $proyecto->id_user === (int) $user->id)
            || ($user->isContratista && (int) $proyecto->id_user_obra_blanca === (int) $user->id);

        abort_unless($permitido, 403, 'No tiene acceso a este proyecto.');
    }

    /**
     * CAMBIO: recibe ahora dos fuentes por separado.
     *  - $valorProyecto   = fila PlanillaValorProyecto.tipo=1 (puede ser null)
     *  - $entregableTipo5 = fila PlanillaEntregables.tipo=5   (puede ser null)
     */
    private function armarCobros($valorProyecto, $entregableTipo5): array
    {
        $resultado = [
            'activo'       => false,
            'hay'          => false,
            'puedeGuardar' => false,
            'items'        => [],
            'totales'      => [
                'cobrado'     => 0,
                'aprobado'    => 0,
                'pagado'      => 0,
                'por_pagar'   => 0,
                'n_aprobados' => 0,
            ],
        ];

        if (!$valorProyecto || !$entregableTipo5) {
            return $resultado;
        }

        $puede     = $this->permisosCobroPorRol(Auth::user());
        $valorBase = (float) $valorProyecto->valor;

        $resultado['activo'] = true;

        foreach ($entregableTipo5->items as $idx => $item) {
            // NUEVO: Calcular el tope por cada ítem
            $tope = PlanillaEntregables::montoItem($item, $valorBase);
            
            $c = PlanillaEntregables::normalizarCobro($item);

            $bloqueado  = $c['fecha_pago'] !== null;
            $tieneValor = $c['valor_cobrar'] !== null;

            $canValor = $puede['valor'] && !$bloqueado && $c['aprobacion'] === 0;
            $canAprob = $puede['aprob'] && !$bloqueado && $tieneValor;
            //$canFecha = $puede['fecha'] && !$bloqueado && $tieneValor && $c['aprobacion'] > 0;
            $canFecha = $puede['fecha'] && $tieneValor && $c['aprobacion'] > 0;
            $aprobado = PlanillaEntregables::valorAprobado($c['valor_cobrar'], $c['aprobacion']);

            if ($bloqueado) {
                $estado = 'pagado';
            } elseif ($c['aprobacion'] > 0) {
                $estado = 'aprobado';
            } elseif ($tieneValor) {
                $estado = 'cobrado';
            } else {
                $estado = 'pendiente';
            }

            if ($tieneValor) {
                $resultado['hay'] = true;
                $resultado['totales']['cobrado'] += $c['valor_cobrar'];
            }

            if ($c['aprobacion'] > 0) {
                $resultado['totales']['n_aprobados']++;
                $resultado['totales']['aprobado'] += $aprobado;

                if ($bloqueado) {
                    $resultado['totales']['pagado'] += $aprobado;
                } else {
                    $resultado['totales']['por_pagar'] += $aprobado;
                }
            }

            if ($canValor || $canAprob || $canFecha) {
                $resultado['puedeGuardar'] = true;
            }

            $resultado['items'][] = [
                'idx'            => $idx,
                'concepto'       => $item['concepto'] ?? 'Sin concepto',
                'en_pesos'       => (int) ($item['en_pesos'] ?? 0) === 1,
                'porcentage'     => (float) ($item['porcentage'] ?? 0),
                'tope'           => $tope, // Usa el tope calculado aquí
                'valor_cobrar'   => $c['valor_cobrar'],
                'esMitad'        => $tieneValor && ($c['valor_cobrar'] == ($tope / 2)), // Usa el tope correcto
                'aprobacion'     => $c['aprobacion'],
                'fecha_pago'     => $c['fecha_pago'],
                'valor_aprobado' => $aprobado,
                'estado'         => $estado,
                'canValor'       => $canValor,
                'canAprob'       => $canAprob,
                'canFecha'       => $canFecha,
            ];
        }

        return $resultado;
    }

    public function pdfConfigPlanilla($tipo, $idProyecto)
    {
        Gate::authorize('planilla.pdfConfigPlanilla');

        $proyecto = Proyecto::findOrFail($idProyecto);

        // CAMBIO: valores numéricos vienen de PlanillaValorProyecto
        $valoresProyecto = PlanillaValorProyecto::where('id_proyecto', $idProyecto)->get()->keyBy('tipo');

        // CAMBIO: los porcentajes (antes tipo 3 de config) vienen de PlanillaEntregables.tipo=5
        $entregableTipo5 = PlanillaEntregables::where('id_proyecto', $idProyecto)->where('tipo', 5)->first();

        // 1. Obtener el valor base del Tipo 1 o Tipo 2
        $valorBase = 0;
        if ($tipo == 1 && isset($valoresProyecto[1])) {
            $valorBase = $valoresProyecto[1]->valor;
        } elseif ($tipo == 2 && isset($valoresProyecto[2])) {
            $valorBase = $valoresProyecto[2]->valor;
        }

        // 2. Calcular los porcentajes del tipo 5 aplicados al valor base
        $porcentajes = [];
        $totalDesglose = 0;

        // CAMBIO: $txTipo viene ahora de PlanillaValorProyecto
        $txTipo = PlanillaValorProyecto::$txTipo[$tipo] ?? 'Configuración General';

        if ($entregableTipo5) {
            foreach ($entregableTipo5->items as $item) {
                $porcentaje = (float) ($item['porcentage'] ?? 0);

                if ((int) ($item['en_pesos'] ?? 0) === 0) {
                    $montoCalculado = $valorBase * ($porcentaje / 100);
                } else {
                    $montoCalculado = $porcentaje;
                }

                $porcentajes[] = [
                    'concepto'   => $item['concepto'] ?? 'Sin concepto',
                    'porcentaje' => $porcentaje,
                    'monto'      => $montoCalculado,
                    'en_pesos'   => $item['en_pesos'] ?? 0,
                ];

                $totalDesglose += $montoCalculado;
            }
        }

        // CAMBIO: entregables vienen de la nueva tabla con items JSON
        $dataEntrePlanilla = PlanillaEntregables::where('id_proyecto', $idProyecto)
            ->whereIn('tipo', [1, 2, 3, 4])
            ->get();

        $txTipoEntre = PlanillaEntregables::$txTipo;
        $arr = [];
        $valorTotal = 0;

        foreach ($dataEntrePlanilla as $row) {
            $area = $txTipoEntre[$row->tipo] ?? '';
            if (!isset($arr[$area])) {
                $arr[$area] = [
                    "espacio"  => $area,
                    "items"    => [],
                    "subtotal" => 0,
                ];
            }

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