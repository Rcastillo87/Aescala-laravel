@extends('layouts.app')
@section('content')

    @include('tareas.filter')
    <div class="flex justify-start items-start gap-4 mb-2 w-full">
        <div class="lg:flex gap-3">
            <span class="flex items-center">
                Normal (<=80%)
                <hr class="border-2 bg-green-500 rounded-lg w-[55px] p-[3px] ml-1">
            </span>
            <span class="flex items-center">
                Próximos a vencer (80-100%)
                <hr class="border-2 bg-orange-400 rounded-lg w-[55px] p-[3px] ml-1">
            </span>

            <span class="flex items-center">
                Atrasados (=> 100%)
                <hr class="border-2 bg-red-500 rounded-lg w-[55px] p-[3px] ml-1">
            </span>
        </div>
    </div>

    @php
        $proyectos = '';
        foreach ($proyecto as $proy) {
            $card = 
            "<div data-id='$proy->id' draggable='true' class='draggable card-proyecto cursor-pointer w-[280px] border-gray-400 border bg-white rounded-md text-start proys-center px-2 py-1'>
                <p class='text-sm font-bold'>Proyecto: <span class='text-md font-semibold text-gray-600'>$proy->nombre_proyecto</span></p>
                <p class='text-sm font-bold'>Cliente: <span class='text-md font-semibold text-gray-600'>$proy->nombre_cliente</span></p>
                <p class='text-sm font-bold'>Arquitecto: <span class='text-md font-semibold text-gray-600'>".strtolower($proy->user->nombre_completo?? 'Sin asignar')."</span></p>
                <p class='text-sm font-bold'>Estado: <span class='text-md font-semibold text-gray-600'>$proy->spanEstado</span></p>
            </div>";
            $proyectos .= $card;
        }
        
        $arratareas = [];
        foreach ($items as $item) {
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
            
            $card = 
            "<div data-id='$item->id_proyecto' draggable='true' data-tipo='$item->id_tarea_tipo' class='draggable card-proyecto cursor-pointer w-[280px] border-gray-400 border rounded-md text-start items-center px-2 py-1'>
                <p class='text-sm font-bold'>Proyecto: <span class='text-md font-semibold text-gray-600'>".$item->proyecto->nombre_proyecto."</span></p>
                <p class='text-sm font-bold'>Cliente: <span class='text-md font-semibold text-gray-600'>".$item->proyecto->nombre_cliente."</span></p>
                <p class='text-sm font-bold'>Proyecto Estado: <span class='text-md font-semibold text-gray-600'>".$item->proyecto->spanEstado."</span></p>
                <p class='text-sm font-bold'>Tarea: <span class='text-md font-semibold text-gray-600'>".$item->tareaTipo->nombre_tarea."</span></p>
                <p class='text-sm font-bold'>Tarea Estado: <span class='text-md font-semibold text-gray-600'>".$item->spanEstado."</span></p>
                <p class='text-sm font-bold'>Arquitecto: <span class='text-md font-semibold text-gray-600'>".strtolower($item->user->nombre_completo?? 'Sin asignar')."</span></p>
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
            if (isset($arratareas[$item->id_tarea_tipo])) {
                $arratareas[$item->id_tarea_tipo] .= $card;
            } else {
                $arratareas[$item->id_tarea_tipo] = $card;
            }
        }
    @endphp

    <div id="scrollContainer" class="flex w-full lg:h-[calc(100vh-355px)] h-[calc(100vh-410px)] gap-2 overflow-x-auto">

        <div class="flex flex-col flex-shrink-0 min-w-[290px] bg-blue-50 border rounded-md">
            <h3 class="text-xl font-bold mb-3 text-center">Sin Tareas</h3>
            <div class="@if(Auth::user()->isNotColab) task-list tarea-column @endif h-full overflow-y-auto space-y-2 p-1">
                {!! $proyectos !!}
            </div>
        </div>

        @foreach ($tareaTipo as $tarea)
            <div class="flex flex-col flex-shrink-0 min-w-[290px] bg-white border rounded-md">
                <h3 class="text-xl font-bold mb-3 text-center">{{ $tarea['nombre_tarea'] }}</h3>
                <div data-id="{{ $tarea['id'] }}" data-name='{{ $tarea['nombre_tarea'] }}' id='{{ $tarea['id'] }}'
                    class="@if(Auth::user()->isNotColab) task-list @endif h-full overflow-y-auto space-y-2 p-1">
                    @if(!empty( $arratareas[$tarea['id']] ))
                        {!! $arratareas[$tarea['id']] !!}
                    @endif
                </div>
            </div>
        @endforeach

        <div data-id="X" data-name="Finalizado" 
            class="@if(Auth::user()->isNotColab) task-end @endif flex flex-col flex-shrink-0 min-w-[290px] bg-red-50 border rounded-md">
            <h3 class="text-xl font-bold mb-3 text-center">Fin</h3>
        </div>

    </div>


    @include('proyecto.modalAvances')
    @include('proyecto.modalTarea')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="{{ asset('js/tareas/index.js') }}?v={{ filemtime(public_path('js/tareas/index.js')) }}"></script>
@endsection