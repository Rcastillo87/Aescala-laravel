<?php

namespace App\Http\Controllers;

use App\Models\ConfigAdicionales;
use App\Models\ConfigPorcentajes;
use App\Models\ValorArea;
use App\Http\Requests\RequestConfigAdicional;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ConfiguracionController extends Controller
{
    private const MODELS = [
        'adicionales' => ConfigAdicionales::class,
        'porcentajes' => ConfigPorcentajes::class,
        'valor-area'  => ValorArea::class,
    ];

    private function resolveModel(string $type): string
    {
        if (!isset(self::MODELS[$type])) {
            abort(400, 'Tipo de configuración inválido');
        }

        return self::MODELS[$type];
    }

    private function getConfigData(string $modelClass): array
    {
        return [
            'años' => $modelClass::select('año')
                ->distinct()
                ->orderByDesc('año')
                ->pluck('año'),
            'configActual' => $modelClass::where('año', now()->year)->get(),
        ];
    }

    public function listConfigYear(string $type, int $anio): JsonResponse
    {
        $model = $this->resolveModel($type);
        return response()->json([
            'status' => true,
            'data'   => $model::where('año', $anio)->get(),
        ]);
    }

    public function saveConfigAdicional(string $type, RequestConfigAdicional $request)
    {
        return $this->save($type, $request->validated());
    }

    private function save(string $type, array $data)
    {
        $model = $this->resolveModel($type);
        $isUpdate = !empty($data['id']);
        $message = $isUpdate ? 'Lista editada con éxito' : 'Lista creada con éxito';
        try {
            DB::transaction(function () use ($model, $data, $isUpdate) {
                if ($isUpdate) {
                    $model::whereKey($data['id'])->update($data);
                } else {
                    $model::create($data);
                }
            });
            return back()->with('success', $message);
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Error al guardar la configuración');
        }
    }

    public function indexConfigAdicional()
    {
        return view(
            'configuracion.indexConfigAdicional',
            array_merge(
                ['title' => 'Configuración de Adicionales'],
                $this->getConfigData(ConfigAdicionales::class)
            )
        );
    }

    public function indexConfigPorcentaje()
    {
        return view(
            'configuracion.indexConfigPorcentaje',
            array_merge(
                ['title' => 'Configuración de Porcentajes'],
                $this->getConfigData(ConfigPorcentajes::class)
            )
        );
    }

    public function indexValorArea()
    {
        return view(
            'configuracion.indexValorArea',
            array_merge(
                ['title' => 'Configuración de Valor por Área'],
                $this->getConfigData(ValorArea::class)
            )
        );
    }
}