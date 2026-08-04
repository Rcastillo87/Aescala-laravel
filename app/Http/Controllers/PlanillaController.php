<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\PlanillaEntregables;
use App\Models\OtroSi;

use App\Http\Requests\SavePlantillaRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;


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

        $tipos = PlanillaEntregables::$txTipo;
        $unidades = OtroSi::$unidades;

        $title = 'Planilla del Proyecto';
        //dd($planillaEntregables->toArray());

        return view('planilla.planillaProyecto', 
            compact('proyecto', 'departamentos', 'title', 'planillaEntregables', 'otroSi', 'tipos', 'unidades'));
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

}
