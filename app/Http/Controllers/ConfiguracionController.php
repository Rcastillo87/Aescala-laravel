<?php

namespace App\Http\Controllers;

use App\Models\ConfigAdicionales;
use App\Models\ConfigPorcentajes;
use App\Models\ValorArea;
use App\Http\Requests\saveValorAreaRequest;
use App\Http\Requests\savePorcentajesRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

    private function save(string $type, array $req)
    {
        $model = $this->resolveModel($type);
        $items = $req['items'];
        $year  = $req['select_año'];

        $exists = $model::where('año', $year)->exists();
        $message = $exists
            ? 'Configuración actualizada con éxito'
            : 'Configuración creada con éxito';
        try {
            DB::transaction(function () use ($model, $items, $year) {
                foreach ($items as $item) {
                    $data = array_merge($item, ['año' => $year]);
                    if (!empty($item['id'])) {
                        $model::where('id', $item['id'])->update($data);
                    } else {
                        $model::create($data);
                    }
                }
            });

            return response()->json([
                'status'  => true,
                'message' => $message
            ]);
        } catch (\Throwable $e) {
            dd($e->getMessage());
            report($e);

            return response()->json([
                'status'  => false,
                'message' => 'Error al guardar la configuración'
            ], 500);
        }
    }

    /*funcion de rutas*/
    public function indexValorArea($año)
    {
        $title = 'Configuración: Valor por Área del año ' . $año;
        $items = ValorArea::where('año', $año)->get()->toArray();
        $añoActual = Carbon::now()->year;
        $años0 = ValorArea::select('año')->distinct()->orderByDesc('año')->pluck('año');
        $años = $años0->contains($añoActual)
            ? $años0
            : (clone $años0)->prepend($añoActual);
        return view('configuracion.indexValorArea', compact('title', 'items', 'años', 'años0'));
    }

    public function indexPorcentajes($año)
    {
        $title = 'Configuración: Porcentajes del año ' . $año;
        $items = ConfigPorcentajes::where('año', $año)->get()->toArray();
        $añoActual = Carbon::now()->year;
        $años0 = ConfigPorcentajes::select('año')->distinct()->orderByDesc('año')->pluck('año');
        $años = $años0->contains($añoActual)
            ? $años0
            : (clone $años0)->prepend($añoActual);
        return view('configuracion.indexPorcentajes', compact('title', 'items', 'años', 'años0'));
    }

    public function listConfigYearModel(string $type, int $año): JsonResponse
    {
        $model = $this->resolveModel($type);
        return response()->json([
            'status' => true,
            'data'   => $model::where('año', $año)->get(),
        ]);
    }

    public function saveValorArea(saveValorAreaRequest $request)
    {
        return $this->save('valor-area', $request->validated());
    }

    public function savePorcentajes(savePorcentajesRequest $request)
    {
        return $this->save('porcentajes', $request->validated());
    }
}