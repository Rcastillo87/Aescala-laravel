<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\PlanillaEntregables;

use App\Http\Requests\AdicionalesRequest;


use Illuminate\Support\Facades\Gate;

class PlanillaController extends Controller
{

    public function index($id)
    {
        Gate::authorize('proyecto.index');
        $proyecto = Proyecto::findOrFail($id);
        $departamentos = json_decode(file_get_contents(storage_path('json/jsonCityColombia.json')), true);

        $planillaEntregables = PlanillaEntregables::where('id_proyecto', $id)->get();

        $otroSi = $proyecto->otro_si()->where('estado', 1)->get();

        $title = 'Planilla del Proyecto';

        return view('planilla.planillaProyecto', 
            compact('proyecto', 'departamentos', 'title', 'planillaEntregables', 'otroSi'));
    }

}
