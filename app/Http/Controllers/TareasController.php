<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tarea;
use App\Models\TareaTipo;
use App\Models\Proyecto;
use App\Models\User;
use App\Models\Festivos;

class TareasController extends Controller
{
    public function index( ) 
    {
        $title = 'Lista de Teras';
        $items = Tarea::with(['proyecto', 'tareaTipo', 'user'])
            ->whereHas('proyecto', function ($q) {
                $q->whereIn('id_estado', [1, 5]);
            })
            ->when(request('nombre_proyecto'), function ($query, $nombre_proyecto) {
                $query->whereHas('proyecto', function ($q) use ($nombre_proyecto) {
                    $q->whereRaw('LOWER(nombre_proyecto) LIKE ?', ['%' . strtolower($nombre_proyecto) . '%']);
                });
            })
            ->when(request('id_userSerch'), function ($query, $id_user) {
                $query->where('id_user', $id_user);
            })
            ->when(request('id_tipoSerch'), function ($query, $id_tipo) {
                $query->where('id_tarea_tipo', $id_tipo);
            })
            ->get();


        $tareaTipo = TareaTipo::get(['id', 'nombre_tarea'])->toArray();
        $proyecto = Proyecto::wherein('id_estado', [1, 5])->get(['id', 'nombre_proyecto'])->toArray();
        $userColab = User::where('id_rol', 3)->where('activo', 1)->get(['id', 'nombre_completo'])->toArray();
        $festivos = Festivos::pluck('date')->map(fn($date) => Carbon::parse($date)->toDateString())->toArray();
        $hoy = Carbon::today();
        $estadoTarea = Tarea::$estado;
        $headerAvance = ['Avance', 'Fecha de Ejecucion', 'Fecha Guardado', 'Opciones'];
        return view('tareas.index', compact('title', 'items', 'tareaTipo', 'proyecto', 'userColab', 'festivos', 'hoy', 'headerAvance', 'estadoTarea'));
    }

    public function editTarea ($id)
    {
        $tarea = Tarea::find($id);
        if($tarea){
            return response()->json([
                'status' => true,
                'message' => 'Lista de tareas.',
                'data' => $tarea
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No se encontro tarea.'
            ], 404);
        }
    }
    
}