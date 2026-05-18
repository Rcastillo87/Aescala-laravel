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

    private function cardTarea($item)
    {
        $festivos = Festivos::pluck('date')
            ->map(fn($date) => Carbon::parse($date)->toDateString())
            ->toArray();

        $hoy = Carbon::today();
        $diasTrascurridosTarea = $item->diasHabilesTrascurridos($hoy, $festivos) ?? 0;
        $diasTarea             = $item->dias_trabajo ?? 0;

        if ($diasTarea == 0) {
            $porcenTarea = 100;
        } else {
            $porcenTarea = intval(($diasTrascurridosTarea * 100) / ((int) $diasTarea));
        }

        /* ── Colores semánticos del bloque de progreso ─────────────── */
        [$progBg, $progBarBg, $progBarFill, $progFracColor, $progPctColor] = match (true) {
            $porcenTarea <= 80 => ['bg-green-50',  'bg-green-200',  'bg-green-500',  'text-green-900', 'text-green-600'],
            $porcenTarea < 100 => ['bg-orange-50', 'bg-orange-200', 'bg-orange-500', 'text-orange-900','text-orange-500'],
            default            => ['bg-red-50',    'bg-red-200',    'bg-red-500',    'text-red-900',   'text-red-600'],
        };

        $barWidth = min($porcenTarea, 100);

        /* ── Visibilidad de botones según rol ──────────────────────── */
        $hidden = Auth::user()->isNotColab ? '' : 'hidden';

        /* ── SVG reutilizables ─────────────────────────────────────── */
        $svgEdit = "<svg class='w-3 h-3' fill='none' viewBox='0 0 24 24'>
            <path stroke='currentColor' stroke-linecap='round' stroke-linejoin='round' stroke-width='2'
                d='m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0
                   1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565
                   6.844-6.844a2.015 2.015 0 0 1 2.852 0Z'/>
        </svg>";

        $svgAvance = "<svg class='w-3 h-3' fill='none' viewBox='0 0 24 24'>
            <path stroke='currentColor' stroke-linecap='round' stroke-linejoin='round' stroke-width='2'
                d='M15 4h3a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h3m0
                   3h6m-3 5h3m-6 0h.01M12 16h3m-6 0h.01M10 3v4h4V3h-4Z'/>
        </svg>";

        $svgCal = "<svg class='inline-block mr-0.5' width='10' height='10' fill='none' viewBox='0 0 24 24'>
            <path stroke='currentColor' stroke-width='2' stroke-linecap='round'
                d='M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2
                   2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z'/>
        </svg>";

        return
        "<div data-id='$item->id_proyecto' data-new='0' draggable='true' data-tipo='$item->id_tarea_tipo'
            class='draggable card-proyecto cursor-pointer w-full bg-white border border-gray-200
                   rounded-xl shadow-sm hover:shadow-md hover:border-blue-300
                   transition-all duration-200 px-3 py-2.5'>

            <p class='text-sm font-semibold text-gray-800 truncate mb-1.5'>".$item->proyecto->nombre_proyecto."</p>

            <div class='grid grid-cols-[auto_1fr] gap-x-2 gap-y-1 mb-2'>
                <span class='text-[11px] text-gray-400'>Proy. estado</span>
                <span class='text-[11px]'>".$item->proyecto->spanEstado."</span>

                <span class='text-[11px] text-gray-400'>Tarea</span>
                <span class='text-[11px] font-medium text-gray-700 truncate'>".$item->tareaTipo->nombre_tarea."</span>

                <span class='text-[11px] text-gray-400'>Tarea estado</span>
                <span class='text-[11px]'>".$item->spanEstado."</span>

                <span class='text-[11px] text-gray-400'>Encargado</span>
                <span class='text-[11px] font-medium text-gray-700 truncate'>".strtolower($item->user->nombre_completo)."</span>
            </div>

            <hr class='border-t border-gray-100 mb-2'>

            <div class='rounded-lg ".$progBg." px-3 py-2 mb-2'>
                <div class='flex justify-between items-baseline mb-1.5'>
                    <span class='text-[11px] font-medium ".$progFracColor."'>".$diasTrascurridosTarea." / ".$diasTarea." días</span>
                    <span class='text-lg font-bold leading-none ".$progPctColor."'>".$porcenTarea."%</span>
                </div>
                <div class='w-full h-1.5 rounded-full ".$progBarBg." mb-1.5 overflow-hidden'>
                    <div class='h-1.5 rounded-full ".$progBarFill."' style='width:".$barWidth."%'></div>
                </div>
                <div class='flex gap-3'>
                    <span class='text-[10px] ".$progFracColor." flex items-center gap-0.5'>
                        $svgCal Ini: ".$item->fecIni."
                    </span>
                    <span class='text-[10px] ".$progFracColor." flex items-center gap-0.5'>
                        $svgCal Fin: ".$item->fechaFin."
                    </span>
                </div>
            </div>

            <div class='".$hidden." flex gap-2'>
                <a tabindex='0'
                    data-tooltip-target='tooltip-hover-edit-".$item->id."'
                    data-tooltip-trigger='hover'
                    onclick='editTarea(".$item->id.")'
                    x-data=''
                    class='flex-1 flex items-center justify-center gap-1.5 py-1.5 rounded-lg
                           bg-green-50 border border-green-300 text-green-700
                           hover:bg-green-100 transition-colors duration-150 cursor-pointer
                           text-[11px] font-medium'>
                    $svgEdit Editar
                </a>

                <a tabindex='0'
                    data-tooltip-target='tooltip-hover-".$item->id."'
                    data-tooltip-trigger='hover'
                    x-data=''
                    x-on:click=\"\$dispatch('open-modal', 'avance-modal')\"
                    onclick='openAvance(0, ".$item->id.")'
                    class='flex-1 flex items-center justify-center gap-1.5 py-1.5 rounded-lg
                           bg-violet-50 border border-violet-300 text-violet-700
                           hover:bg-violet-100 transition-colors duration-150 cursor-pointer
                           text-[11px] font-medium'>
                    $svgAvance Avances
                </a>
            </div>

            <div id='tooltip-hover-edit-".$item->id."' role='tooltip'
                class='absolute z-10 inline-block px-2 py-1 text-xs font-medium border
                       bg-white text-gray-800 rounded-lg shadow opacity-0 invisible tooltip'
                data-popper-placement='top'>
                Editar<div class='tooltip-arrow' data-popper-arrow></div>
            </div>
            <div id='tooltip-hover-".$item->id."' role='tooltip'
                class='absolute z-10 invisible inline-block px-2 py-1 text-xs font-medium border
                       bg-white text-gray-800 rounded-lg shadow opacity-0 tooltip'
                data-popper-placement='top'>
                Avances<div class='tooltip-arrow' data-popper-arrow></div>
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
