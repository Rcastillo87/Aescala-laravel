<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Support\Facades\Gate;

class PlanillaController extends Controller
{

    public function index($id)
    {
        Gate::authorize('proyecto.index');
        $proyecto = Proyecto::findOrFail($id);
        $departamentos = json_decode(file_get_contents(storage_path('json/jsonCityColombia.json')), true);

        $title = 'Planilla del Proyecto';

        return view('planilla.planillaProyecto', compact('proyecto', 'departamentos', 'title'));
    }

}
