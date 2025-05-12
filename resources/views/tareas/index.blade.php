@extends('layouts.app')
@section('content')

    @include('tareas.filter')
    <div class="flex justify-between items-start gap-4 w-full">
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
        <div class="flex justify-end text-center mb-3">
            <x-secondary-button 
                onclick="formIdProyecto()"
                x-data="" data-tooltip-trigger="hover" 
                x-on:click="$dispatch('open-modal', 'my-modal')"
                class="mt-4 md:mt-0 cursor-pointer">
                Crear Tareas
            </x-secondary-button>
        </div>
    </div>

    @php
        $div1 = '';
        $div2 = '';
        $div3 = '';
        $div4 = '';
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

            $card = 
            "<div class='w-[291px] border-gray-400 border rounded-md text-start items-center px-2 py-1'>
                <p class='text-lg font-bold'>Proyecto: <span class='text-md font-semibold text-gray-600'>".$item->proyecto->nombre_proyecto."</span></p>
                <p class='text-lg font-bold'>Tarea: <span class='text-md font-semibold text-gray-600'>".$item->tareaTipo->nombre_tarea."</span></p>
                <p class='text-lg font-bold'>Encargado: <span class='text-md font-semibold text-gray-600'>".strtolower($item->user->nombre_completo)."</span></p>
                <p class='text-lg font-bold'>Estado: <span class='text-md font-semibold text-gray-600'>".$item->spanEstado."</span></p>

                <div class='flex my-2'>
                    <div class='flex flex-col items-center justify-center border-2 rounded-xl w-[180px] ".$bgTarea." p-2'>
                        <p class='text-white text-2xl font-bold'>".$diasTrascurridosTarea."/".$diasTarea." | ". $porcenTarea ."%</p>
                        <span class='text-white text-md'>F Ini: ".$item->fecIni."</span>
                        <span class='text-white text-md'>F Fin: ".$item->fechaFin."</span>
                    </div>

                    <div class='mx-auto text-center gap-1'>
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
                    
                        <!-- Botón Cambio de Estado 
                        <div class='relative inline-flex'>
                            <a tabindex='0' data-tooltip-target='tooltip-hover-112' data-tooltip-trigger='hover' onclick='cambiarEstado(112, 1)' class='flex items-center justify-center w-10 h-10 text-white bg-violet-700 hover:bg-white hover:text-violet-800 border-2 border-violet-800 focus:ring-4 
                                      focus:outline-none focus:ring-violet-300 font-medium rounded-full text-sm dark:bg-violet-600 dark:hover:bg-violet-700 dark:focus:ring-violet-800 cursor-pointer'>
                                <svg class='w-5 h-5' xmlns='http://www.w3.org/2000/svg' width='24' height='24' fill='none' viewBox='0 0 24 24'>
                                    <path stroke='currentColor' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 20V7m0 13-4-4m4 4 4-4m4-12v13m0-13 4 4m-4-4-4 4'></path>
                                </svg>                    
                            </a>
                            <div id='tooltip-hover-112' role='tooltip' class='absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700' style='position: absolute; inset: auto auto 0px 0px; margin: 0px; transform: translate(-17.5px, -47.5px);' data-popper-placement='top'>
                                Cambio de Estado
                                <div class='tooltip-arrow' data-popper-arrow=' style='position: absolute; left: 0px; transform: translate(32.5px, 0px);'></div>
                            </div>
                        </div>-->
                        
                    </div>
                </div>
            </div>";

            if(in_array($item->id_tarea_estado, [10])){
                $div1 .= $card;
            }
            if(in_array($item->id_tarea_estado, [4, 3, 7])){
                $div2 .= $card;
            }
            if(in_array($item->id_tarea_estado, [1, 2, 5])){
                $div3 .= $card;
            }
            if(in_array($item->id_tarea_estado, [6, 8, 9])){
                $div4 .= $card;
            }
        }
    @endphp

    <div class="h-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="border items-center rounded-md p-4 flex flex-col space-y-2 overflow-x-scroll overflow-y-scroll">
            <h3 class="text-xl font-bold mb-3">Planificacion</h3>
            {!! $div1 !!}
        </div>
        <div class="border items-center rounded-md p-4 flex flex-col space-y-2 overflow-x-scroll overflow-y-scroll">
            <h3 class="text-xl font-bold mb-3">Obra Negra</h3>
            {!! $div2 !!}
        </div>
        <div class="border items-center rounded-md p-4 flex flex-col space-y-2 overflow-x-scroll overflow-y-scroll">
            <h3 class="text-xl font-bold mb-3">Obra Blanca</h3>
            {!! $div3 !!}
        </div>
        <div class="border items-center rounded-md p-4 flex flex-col space-y-2 overflow-x-scroll overflow-y-scroll">
            <h3 class="text-xl font-bold mb-3">Otro</h3>
            {!! $div4 !!}
        </div>
    </div>
    @include('proyecto.modalAvances')
    @include('proyecto.modalTarea')
@endsection

@section('scripts')
    <script src="{{asset('js/tareas/index.js')}}"></script>
@endsection