<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tarea;
use App\Models\TareaTipo;
use App\Models\Proyecto;
use App\Models\User;
use App\Models\Festivos;
use App\Models\Avance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TareasController extends Controller
{
    public function index()
    {
        Gate::authorize('tareas.index');
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

        $tareaTipo = TareaTipo::where('orden', '<>', 0)
            ->orderBy('orden', 'asc')
            ->get(['id', 'nombre_tarea'])
            ->toArray();


        $proyecto = Proyecto::with('tareas')
            ->when($cola, function ($query, $id_user) {
                $query->where('id_user', $id_user);
            })
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

    public function editTarea($id)
    {
        Gate::authorize('tareas.editTarea');
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

    public function deleteTarea ()
    {
        Gate::authorize('tareas.deleteTarea');
        $tarea = Tarea::find(Request('id'));
        $idPro = $tarea->id_proyecto;
        if($tarea){
            Avance::where('id_tarea', Request('id'))->delete();
            $tarea->delete();
            if (!Tarea::where('id_tarea_estado', 2)->where('id_proyecto', $idPro)->exists()) {
                $val = Tarea::where('id_tarea_estado', 3)->where('id_proyecto', $idPro)->orderBy('createdAt', 'desc')->first();
                if ($val) {
                    $val->update(['id_tarea_estado' => 2]);
                }
            }
            return response()->json([
                'status' => true,
                'message' => 'Se elimio la tarea y los avaces de esta.',
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
        Gate::authorize('tareas.moverTarea');
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

        DB::beginTransaction();
        try {

            $idEstado = $pro->id_estado;
            if ($pro->id_estado == 7) {
                $pro->id_estado = 1;
                $pro->save();
                $idEstado = 1;
            }

            $tarea['id_proyecto'] = $req->proyecto_id;
            $tarea['id_user'] = $pro->id_user;
            $tarea['id_tarea_estado'] = 2;
            $tarea['descripccion'] = null;
            $tarea['id_tarea_tipo'] = $req->tarea_tipo_id;
            $tarea['dias_trabajo'] = 10;
            $tarea['save'] = $idEstado;

            $tarea->save();
            if(isset($tareaOld)){
                $tareaOld->id_tarea_estado = 3;
                $tareaOld->fec_fin_real = now();
                $tareaOld->save();
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Tarea asignada correctamente.',
                'data' => $this->cardTarea($tarea)
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

    private function cardTarea($item){
        $festivos = Festivos::pluck('date')->map(fn($date) => Carbon::parse($date)->toDateString())->toArray();
        $hoy = Carbon::today();
        $diasTrascurridosTarea = $item->diasHabilesTrascurridos($hoy, $festivos)??0;
        $diasTarea = $item->dias_trabajo??0;

        if ($diasTarea == 0) {
            $porcenTarea = 100;
        } else {
            $porcenTarea = intval(($diasTrascurridosTarea*100)/((int)$diasTarea));
        }

        $bgTarea = match (true) {
            $porcenTarea <= 80 => 'bg-green-500',
            $porcenTarea < 100 => 'bg-orange-400',
            default => 'bg-red-500',
        };
        $hidden = '';
        if(!Auth::user()->isNotColab){
            $hidden = 'hidden';
        }

        return
        "<div data-id='$item->id_proyecto' data-new='0' draggable='true' data-tipo='$item->id_tarea_tipo' class='draggable card-proyecto cursor-pointer w-[280px] border-gray-400 border rounded-md text-start items-center px-2 py-1'>
            <p class='text-lg font-bold'>Proyecto: <span class='text-md font-semibold text-gray-600'>".$item->proyecto->nombre_proyecto."</span></p>
            <p class='text-lg font-bold'>Proyecto Estado: <span class='text-md font-semibold text-gray-600'>".$item->proyecto->spanEstado."</span></p>
            <p class='text-lg font-bold'>Tarea: <span class='text-md font-semibold text-gray-600'>".$item->tareaTipo->nombre_tarea."</span></p>
            <p class='text-lg font-bold'>Tarea Estado: <span class='text-md font-semibold text-gray-600'>".$item->spanEstado."</span></p>
            <p class='text-lg font-bold'>Encargado: <span class='text-md font-semibold text-gray-600'>".strtolower($item->user->nombre_completo)."</span></p>
            <div class='flex my-2'>
                <div class='flex flex-col items-center justify-center border-2 rounded-xl w-[250px] ".$bgTarea." p-2'>
                    <p class='text-white text-2xl font-bold'>".$diasTrascurridosTarea."/".$diasTarea." | ". $porcenTarea ."%</p>
                    <span class='text-white text-md'>F Ini: ".$item->fecIni."</span>
                    <span class='text-white text-md'>F Fin: ".$item->fechaFin."</span>
                </div>

                <div class='space-y-2 mx-auto text-center gap-1 ".$hidden."'>
                    <!-- Botón Editar -->
                    <div class='relative inline-flex'>
                        <a
                                tabindex='0'
                                data-tooltip-target='tooltip-hover-edit-".$item->id."'
                                data-tooltip-trigger='hover'
                                onclick='editTarea(".$item->id.")'
                                x-data=''
                                class='flex items-center justify-center w-10 h-10 text-white bg-green-700 hover:bg-white hover:text-green-800 border-2 border-green-800 focus:ring-4
                                focus:outline-none focus:ring-green-300 font-medium rounded-full text-sm dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800'>
                            <svg class='w-5 h-5' xmlns='http://www.w3.org/2000/svg' width='24' height='24' fill='none' viewBox='0 0 24 24'>
                                <path stroke='currentColor' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z'></path>
                            </svg>
                        </a>
                        <div id='tooltip-hover-edit-".$item->id."' role='tooltip' class='absolute z-10 inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs tooltip dark:bg-gray-700 opacity-0 invisible' style='position: absolute; inset: auto auto 0px 0px; margin: 0px; transform: translate(-12.5px, -47.5px);' data-popper-placement='top'>
                            Editar
                            <div class='tooltip-arrow' data-popper-arrow=' style='position: absolute; left: 0px; transform: translate(26.25px, 0px);'></div>
                        </div>
                    </div>

                    <!-- Botón Cambio de Estado -->
                    <div class='relative inline-flex'>
                        <a tabindex='0'
                        data-tooltip-target='tooltip-hover-".$item->id."'
                        data-tooltip-trigger='hover'
                        x-data=''
                        x-on:click=\"\$dispatch('open-modal', 'avance-modal')\"
                        onclick='openAvance(0, ".$item->id.")'
                        class='flex items-center justify-center w-10 h-10 text-white bg-violet-700 hover:bg-white hover:text-violet-800 border-2 border-violet-800 focus:ring-4
                                    focus:outline-none focus:ring-violet-300 font-medium rounded-full text-sm dark:bg-violet-600 dark:hover:bg-violet-700 dark:focus:ring-violet-800 cursor-pointer'>
                            <svg class='w-5 h-5' aria-hidden='true' xmlns='http://www.w3.org/2000/svg' width='24' height='24' fill='none' viewBox='0 0 24 24'>
                                <path stroke='currentColor' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 4h3a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h3m0 3h6m-3 5h3m-6 0h.01M12 16h3m-6 0h.01M10 3v4h4V3h-4Z'/>
                            </svg>
                        </a>
                        <div id='tooltip-hover-".$item->id."' role='tooltip' class='absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700' style='position: absolute; inset: auto auto 0px 0px; margin: 0px; transform: translate(-17.5px, -47.5px);' data-popper-placement='top'>
                            Avances de la Tarea
                            <div class='tooltip-arrow' data-popper-arrow=' style='position: absolute; left: 0px; transform: translate(32.5px, 0px);'></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>";
    }

    public function finTarea(Request $req){
        Gate::authorize('tareas.finTarea');
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
