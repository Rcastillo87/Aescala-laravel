<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\PlanillaEntregables;
use App\Models\Otrosi;

use App\Models\ConfigPorcentajes;
use App\Models\ValorArea;
use App\Models\ValorAreaEnchape;
use App\Models\PlanillaConfigProyecto;

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
        // CAMBIO: antes 'proyecto.index'. Ahora entran Administrador, Residente y Contratista.
        Gate::authorize('planilla.index');
        $proyecto = Proyecto::findOrFail($id);

        // CAMBIO: residente y contratista solo ven sus proyectos asignados.
        $this->autorizarProyecto($proyecto);

        $departamentos = json_decode(file_get_contents(storage_path('json/jsonCityColombia.json')), true);

        // CAMBIO: la vista usaba $ubicacion pero nunca se enviaba (siempre mostraba N/A).
        $ubicacion = Proyecto::$ubicacion;

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
                            'porcentage'      => (int) $item->porcentage
                        ];

                    })->values()->toArray(),
                ];

            })
            ->values()
            ->toArray();

        $otroSi = $proyecto->otro_si()->where('estado', 1)->get();

        $valOtrosis = $proyecto->TotalOtroSi?? 0;

        $tipos = PlanillaEntregables::$txTipo;
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

        $configProyecto = PlanillaConfigProyecto::where('id_proyecto', $id)->get()->keyBy('tipo');

        // NUEVO: datos de la sección "Cobros del Proyecto" y su consolidado.
        $cobros = $this->armarCobros($configProyecto);

        $title = 'Planilla del Proyecto';

        return view('planilla.planillaProyecto', 
            compact('proyecto', 'departamentos', 'ubicacion', 'title', 'planillaEntregables', 'otroSi', 'tipos', 'unidades', 
            'configProyecto', 'dataValorArea', 'dataValorAreaEnchape', 'dataConfigPorcentajes', 'valOtrosis', 'cobros'));
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
                        'porcentage'   => $entregable['porcentage'],
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

    /* ==========================================================
     *  COBROS POR ÍTEM (Tipo 3)
     * ========================================================== */

    /**
     * Guarda valor a cobrar, aprobación y fecha de pago.
     * Cada rol solo puede cambiar sus campos; cualquier otro cambio se rechaza.
     *
     *  - Valor a cobrar : Contratista y Administrador. Solo con aprobación en 0 y sin fecha de pago.
     *  - Aprobación     : Residente y Administrador. Requiere valor guardado y sin fecha de pago.
     *  - Fecha de pago  : Solo Administrador. Requiere valor y aprobación 50/100. Una vez guardada
     *                     el ítem queda bloqueado.
     */
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
            // Bloqueo de filas: evita que dos roles guarden a la vez y se pisen entre sí.
            $cfg1 = PlanillaConfigProyecto::where('id_proyecto', $proyecto->id)->where('tipo', 1)->lockForUpdate()->first();
            $cfg3 = PlanillaConfigProyecto::where('id_proyecto', $proyecto->id)->where('tipo', 3)->lockForUpdate()->first();

            if (!$cfg1 || !$cfg3) {
                throw new \DomainException('Debe existir la configuración aceptada de Presupuesto por Proyecto y Porcentajes.');
            }

            $valorBase = (float) $cfg1->valor;
            $items     = $cfg3->valor; // arreglo de ítems

            foreach ($data['cobros'] as $idx => $in) {
                $idx = (int) $idx;

                if (!isset($items[$idx]) || !is_array($items[$idx])) {
                    throw new \DomainException('Uno de los ítems enviados no existe. Recargue la página e intente de nuevo.');
                }

                $etq  = 'Ítem "' . ($items[$idx]['concepto'] ?? ($idx + 1)) . '"';
                $tope = PlanillaConfigProyecto::montoItem($items[$idx], $valorBase);
                $c    = PlanillaConfigProyecto::normalizarCobro($items[$idx]);

                // Si se devuelve a "Sin aprobar", primero se aplica la aprobación
                // para que el valor quede desbloqueado en la misma operación.
                $revierte = array_key_exists('aprobacion', $in)
                    && $in['aprobacion'] !== null
                    && (int) $in['aprobacion'] === 0
                    && $c['aprobacion'] > 0;

                if ($revierte) {
                    $this->aplicarAprobacion($c, $in, $etq, $puede['aprob'], $userId, $ahora);
                    $this->aplicarValor($c, $in, $tope, $etq, $puede['valor'], $userId);
                } else {
                    $this->aplicarValor($c, $in, $tope, $etq, $puede['valor'], $userId);
                    $this->aplicarAprobacion($c, $in, $etq, $puede['aprob'], $userId, $ahora);
                }
                $this->aplicarFecha($c, $in, $etq, $puede['fecha'], $userId);

                $items[$idx] = array_merge($items[$idx], $c);
            }

            $cfg3->valor_config = json_encode(array_values($items));
            $cfg3->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Cobros guardados correctamente.'
            ], 200);
        } catch (\DomainException $e) {
            // Error de regla de negocio: mensaje claro para el usuario.
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

    private function aplicarAprobacion(array &$c, array $in, string $etq, bool $rolPuede, int $userId, string $ahora): void
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

        $c['aprobacion']     = $nuevo;
        $c['aprobacion_por'] = $userId;
        $c['aprobacion_at']  = $ahora;
    }

    private function aplicarFecha(array &$c, array $in, string $etq, bool $rolPuede, int $userId): void
    {
        if (!array_key_exists('fecha_pago', $in)) {
            return;
        }

        $nueva = !empty($in['fecha_pago']) ? $in['fecha_pago'] : null;

        if ($nueva === $c['fecha_pago']) {
            return;
        }

        if (!$rolPuede || $c['fecha_pago'] !== null) {
            throw new \DomainException("$etq: la fecha de pago no se puede modificar.");
        }

        if ($c['aprobacion'] <= 0 || $c['valor_cobrar'] === null) {
            throw new \DomainException("$etq: la fecha de pago requiere valor a cobrar y aprobación (50% o 100%).");
        }

        $c['fecha_pago']     = $nueva;
        $c['fecha_pago_por'] = $userId;
    }

    /**
     * Qué campos de cobro puede tocar el usuario según su rol.
     */
    private function permisosCobroPorRol($user): array
    {
        return [
            'valor' => (bool) ($user->isAdmin || $user->isContratista),
            'aprob' => (bool) ($user->isAdmin || $user->isColab),   // isColab = Residente (rol 3)
            'fecha' => (bool) $user->isAdmin,
        ];
    }

    /**
     * Administrador: cualquier proyecto.
     * Residente: solo donde es el residente asignado (id_user).
     * Contratista: solo donde es el contratista asignado (id_user_obra_blanca).
     */
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
     * Arma la lista de ítems con su estado, permisos del usuario actual y
     * los totales del consolidado de pagos aprobados.
     */
    private function armarCobros($configProyecto): array
    {
        $resultado = [
            'activo'       => false,
            'hay'          => false,   // hay al menos un valor a cobrar registrado
            'puedeGuardar' => false,
            'items'        => [],
            'totales'      => [
                'cobrado'     => 0,   // suma de valores a cobrar registrados
                'aprobado'    => 0,   // suma de valores aprobados (50% / 100%)
                'pagado'      => 0,   // aprobados con fecha de pago
                'por_pagar'   => 0,   // aprobados sin fecha de pago
                'n_aprobados' => 0,
            ],
        ];

        // Se habilita cuando Presupuesto por Proyecto (Tipo 1) y Porcentajes (Tipo 3) están aceptados.
        if (!$configProyecto->has(1) || !$configProyecto->has(3)) {
            return $resultado;
        }

        $puede     = $this->permisosCobroPorRol(Auth::user());
        $valorBase = (float) $configProyecto[1]->valor;

        $resultado['activo'] = true;

        foreach ($configProyecto[3]->valor as $idx => $item) {
            $c = PlanillaConfigProyecto::normalizarCobro($item);

            $bloqueado  = $c['fecha_pago'] !== null;
            $tieneValor = $c['valor_cobrar'] !== null;

            $canValor = $puede['valor'] && !$bloqueado && $c['aprobacion'] === 0;
            $canAprob = $puede['aprob'] && !$bloqueado && $tieneValor;
            $canFecha = $puede['fecha'] && !$bloqueado && $tieneValor && $c['aprobacion'] > 0;

            $aprobado = PlanillaConfigProyecto::valorAprobado($c['valor_cobrar'], $c['aprobacion']);

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
                'tope'           => PlanillaConfigProyecto::montoItem($item, $valorBase),
                'valor_cobrar'   => $c['valor_cobrar'],
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
        // CAMBIO: ahora el módulo también lo abren residente y contratista,
        // así que el PDF se restringe explícitamente (queda solo para Administrador).
        Gate::authorize('planilla.pdfConfigPlanilla');

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

                // CAMBIO: un ítem sin la clave 'en_pesos' se trata como porcentaje,
                // igual que en la vista (antes el PDF lo trataba como pesos).
                if ((int) ($item['en_pesos'] ?? 0) === 0) {
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

        $unidades = Otrosi::$unidades;

        // 3. Cargar la vista y generar el PDF
        $pdf = Pdf::loadView('planilla.pdfPlanillaConfig', 
            compact('proyecto', 'valorBase', 'porcentajes', 'totalDesglose', 'tipo', 'txTipo', 
            'adicionales', 'txTipoEntre', 'unidades', 'valorTotal'));

        // Opcional: usar ->download('nombre.pdf') si prefieres descarga directa en lugar de visualización en pestaña (`->stream()`)
        return $pdf->stream('configuracion-planilla-proyecto-' . $proyecto->id . '.pdf');
    }

}