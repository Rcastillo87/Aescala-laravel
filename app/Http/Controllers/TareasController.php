<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tarea;
use App\Models\TareaTipo;
use App\Models\Proyecto;
use App\Models\User;
use App\Models\Festivos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TareasController extends Controller
{
    public function index()
    {
        $year = date('Y');
        $festivos = new Festivos;
        $festivos->festivos($year);
        $festivos->festivos($year+1);

        if(Auth::user()->isnotColab){
            $cola = Request('id_userSerch');
        } else {
            $cola = Auth::user()->id;
        }

        $title = 'Lista de Teras';
        $items = Tarea::with(['proyecto', 'tareaTipo', 'user'])
            ->when(request('nombre_proyecto'), function ($query, $nombre_proyecto) {
                $query->whereHas('proyecto', function ($q) use ($nombre_proyecto) {
                    $q->whereRaw('LOWER(nombre_proyecto) LIKE ?', ['%' . strtolower($nombre_proyecto) . '%']);
                });
            })
            ->when($cola, function ($query, $id_user) {
                $query->where('id_user', $id_user);
            })
            ->when(request('id_tipoSerch'), function ($query, $id_tipo) {
                $query->where('id_tarea_tipo', $id_tipo);
            })
            ->where('id_tarea_estado', 2)
            ->get();

        $tareaTipo = TareaTipo::get(['id', 'nombre_tarea'])->toArray();


        $proyecto = Proyecto::with('tareas')
            ->when(request('nombre_proyecto'), function ($query, $nombre_proyecto) {
                $query->whereRaw('LOWER(nombre_proyecto) LIKE ?', ['%' . strtolower($nombre_proyecto) . '%']);
            })
            ->where(function ($query) {
                $query->where('id_estado', 1)
                    ->doesntHave('tareas');
            })
            ->orWhere(function ($query) {
                $query->where('id_estado', 5)
                    ->where(function ($q) {
                        $q->doesntHave('tareas')
                            ->orWhereDoesntHave('tareas', function ($subq) {
                                $subq->where('save', 5);
                            });
                    });
            })
            ->get();

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

    public function moverTarea(Request $req){
        $query = Tarea::where('id_proyecto', $req->proyecto_id);
        $tipo = TareaTipo::find($req->tarea_tipo_id);
        $pro = Proyecto::find($req->proyecto_id);
        if(!$pro){
            return response()->json([
                'status' => false,
                'message' => 'No se encontro Proyecto.'
            ], 404);
        }

        if(!$tipo){
            return response()->json([
                'status' => false,
                'message' => 'No se encontro Tipo de Tarea.'
            ], 404);
        }

        $tarea =  new Tarea;
        if($query->exists()){
            $tareaOld = Tarea::where('id_proyecto', $req->proyecto_id)
                ->where('id_tarea_estado', 2)
                ->first();
            if($tareaOld){
                $tarea['fec_inicio'] = $tareaOld->fec_fin;
                $tarea['fec_fin'] = (new Festivos)->calcularFechaFin($tareaOld->fec_fin, 10);
            } else {
                $tarea['fec_inicio'] = now();
                $tarea['fec_fin'] = (new Festivos)->calcularFechaFin(now(), 10);
            }
            
        } else {
            $tarea['fec_inicio'] = $pro->fec_inicio;
            $tarea['fec_fin'] = (new Festivos)->calcularFechaFin($pro->fec_inicio, 10);
        }

        $tarea['id_proyecto'] = $req->proyecto_id;
        $tarea['id_user'] = $pro->id_user;
        $tarea['id_tarea_estado'] = 2;
        $tarea['descripccion'] = null;
        $tarea['id_tarea_tipo'] = $req->tarea_tipo_id;
        $tarea['dias_trabajo'] = 10;
        $tarea['save'] = $pro->id_estado;

        DB::beginTransaction();
        try {
            $tarea->save();
            if(isset($tareaOld)){
                $tareaOld->id_tarea_estado = 3;
                $tareaOld->fec_fin_real = now();
                $tareaOld->save();
            }
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Tarea asignada correctamente.'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error inesperado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function finTarea(Request $req){
        $tarea = Tarea::where('id_proyecto', $req->proyecto_id)
            ->where('id_tarea_estado', 2)
            ->first();

        $pro = Proyecto::find($req->proyecto_id);

        if(!$pro){
            return response()->json([
                'status' => false,
                'message' => 'No se encontro Proyecto.'
            ], 404);
        }

        if(!$tarea){
            return response()->json([
                'status' => false,
                'message' => 'No se encontro ninguna tarea activa.'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $tarea->id_tarea_estado = 3;
            $tarea->fec_fin_real = $req->fecha_fin;
            $tarea->save();

            $pro->id_estado = 3;
            $pro->fec_fin_real = $req->fecha_fin;
            $pro->save();
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Tarea asignada correctamente.'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error inesperado: ' . $e->getMessage()
            ], 500);
        }
    }
}