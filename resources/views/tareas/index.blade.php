@extends('layouts.app')
@section('content')

    @include('tareas.filter')

    {{-- Leyenda --}}
    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 mb-3 px-1">
        <span class="flex items-center gap-1.5 text-xs font-medium text-gray-600">
            <span class="inline-block w-7 h-2 rounded-full bg-green-500"></span>
            Normal (&lt;=80%)
        </span>
        <span class="flex items-center gap-1.5 text-xs font-medium text-gray-600">
            <span class="inline-block w-7 h-2 rounded-full bg-orange-400"></span>
            Próximos a vencer (80-100%)
        </span>
        <span class="flex items-center gap-1.5 text-xs font-medium text-gray-600">
            <span class="inline-block w-7 h-2 rounded-full bg-red-500"></span>
            Atrasados (&gt;=100%)
        </span>
    </div>

    @php
        /* ── SVG reutilizables ─────────────────────────────────────── */
        $svgEdit = "<svg class='w-3 h-3' fill='none' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'>
            <path stroke='currentColor' stroke-linecap='round' stroke-linejoin='round' stroke-width='2'
                d='m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0
                   1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565
                   6.844-6.844a2.015 2.015 0 0 1 2.852 0Z'/>
        </svg>";

        $svgAvance = "<svg class='w-3 h-3' fill='none' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'>
            <path stroke='currentColor' stroke-linecap='round' stroke-linejoin='round' stroke-width='2'
                d='M15 4h3a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h3m0
                   3h6m-3 5h3m-6 0h.01M12 16h3m-6 0h.01M10 3v4h4V3h-4Z'/>
        </svg>";

        $svgCal = "<svg class='inline-block mr-0.5' width='10' height='10' fill='none' viewBox='0 0 24 24'>
            <path stroke='currentColor' stroke-width='2' stroke-linecap='round'
                d='M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2
                   2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z'/>
        </svg>";

        /* ── Columna "Sin Tareas" ───────────────────────────────────── */
        $proyectos   = '';
        $cntProyecto = 0;

        foreach ($proyecto as $proy) {
            $cntProyecto++;
            $card =
            "<div data-id='$proy->id' draggable='true'
                class='draggable card-proyecto cursor-pointer w-full bg-white border border-gray-200
                       rounded-xl shadow-sm hover:shadow-md hover:border-blue-300
                       transition-all duration-200 px-3 py-2.5'>

                <p class='text-sm font-semibold text-gray-800 truncate mb-1.5'>$proy->nombre_proyecto</p>

                <div class='grid grid-cols-[auto_1fr] gap-x-2 gap-y-1 mb-2'>
                    <span class='text-[11px] text-gray-400'>Cliente</span>
                    <span class='text-[11px] font-medium text-gray-700 truncate'>$proy->nombre_cliente</span>
                    <span class='text-[11px] text-gray-400'>Residente</span>
                    <span class='text-[11px] font-medium text-gray-700 truncate'>".strtolower($proy->user->nombre_completo ?? 'Sin asignar')."</span>
                    <span class='text-[11px] text-gray-400'>Estado</span>
                    <span class='text-[11px]'>$proy->spanEstado</span>
                </div>
            </div>";
            $proyectos .= $card;
        }

        /* ── Columnas de tareas ─────────────────────────────────────── */
        $arratareas  = [];
        $cntTareas   = [];   // contador por tipo

        foreach ($items as $item) {
            $diasTrascurridosTarea = $item->diasHabilesTrascurridos($hoy, $festivos) ?? 0;
            $diasTarea             = $item->dias_trabajo ?? 0;

            if ($diasTarea == 0) {
                $porcenTarea = 100;
            } else {
                $porcenTarea = intval(($diasTrascurridosTarea * 100) / ((int)$diasTarea));
            }

            /* colores del bloque de progreso */
            [$progBg, $progBarBg, $progBarFill, $progFracColor, $progPctColor] = match (true) {
                $porcenTarea <= 80 => ['bg-green-50',  'bg-green-200',  'bg-green-500',  'text-green-900', 'text-green-600'],
                $porcenTarea < 100 => ['bg-orange-50', 'bg-orange-200', 'bg-orange-500', 'text-orange-900','text-orange-500'],
                default            => ['bg-red-50',    'bg-red-200',    'bg-red-500',    'text-red-900',   'text-red-600'],
            };

            /* ancho de la barra (cap 100%) */
            $barWidth = min($porcenTarea, 100);

            /* visibilidad botones según rol */
            $hidden = Auth::user()->isNotColab ? '' : 'hidden';

            $card =
            "<div data-id='$item->id_proyecto' draggable='true' data-tipo='$item->id_tarea_tipo'
                class='draggable card-proyecto cursor-pointer w-full bg-white border border-gray-200
                       rounded-xl shadow-sm hover:shadow-md hover:border-blue-300
                       transition-all duration-200 px-3 py-2.5'>

                <p class='text-sm font-semibold text-gray-800 truncate mb-1.5'>".$item->proyecto->nombre_proyecto."</p>

                <div class='grid grid-cols-[auto_1fr] gap-x-2 gap-y-1 mb-2'>
                    <span class='text-[11px] text-gray-400'>Cliente</span>
                    <span class='text-[11px] font-medium text-gray-700 truncate'>".$item->proyecto->nombre_cliente."</span>

                    <span class='text-[11px] text-gray-400'>Proy. estado</span>
                    <span class='text-[11px]'>".$item->proyecto->spanEstado."</span>

                    <span class='text-[11px] text-gray-400'>Tarea</span>
                    <span class='text-[11px] font-medium text-gray-700 truncate'>".$item->tareaTipo->nombre_tarea."</span>

                    <span class='text-[11px] text-gray-400'>Tarea estado</span>
                    <span class='text-[11px]'>".$item->spanEstado."</span>

                    <span class='text-[11px] text-gray-400'>Residente</span>
                    <span class='text-[11px] font-medium text-gray-700 truncate'>".strtolower($item->user->nombre_completo ?? 'Sin asignar')."</span>
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
                        x-on:click=\"\$dispatch('open-modal','avance-modal')\"
                        onclick='openAvance(0,".$item->id.")'
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

            /* acumular por tipo */
            $tid = $item->id_tarea_tipo;
            $arratareas[$tid] = ($arratareas[$tid] ?? '') . $card;
            $cntTareas[$tid]  = ($cntTareas[$tid]  ?? 0) + 1;
        }

        /* paleta de colores para columnas (cicla si hay más tipos de tarea) */
        $colPalette = [
            ['bg' => 'bg-purple-50', 'head' => 'bg-purple-100',  'border' => 'border-purple-100',  'title' => 'text-purple-800', 'badge' => 'bg-purple-200 text-purple-900'],
            ['bg' => 'bg-teal-50',   'head' => 'bg-teal-100',    'border' => 'border-teal-100',    'title' => 'text-teal-800',   'badge' => 'bg-teal-200 text-teal-900'],
            ['bg' => 'bg-amber-50',  'head' => 'bg-amber-100',   'border' => 'border-amber-100',   'title' => 'text-amber-800',  'badge' => 'bg-amber-200 text-amber-900'],
            ['bg' => 'bg-sky-50',    'head' => 'bg-sky-100',     'border' => 'border-sky-100',     'title' => 'text-sky-800',    'badge' => 'bg-sky-200 text-sky-900'],
            ['bg' => 'bg-pink-50',   'head' => 'bg-pink-100',    'border' => 'border-pink-100',    'title' => 'text-pink-800',   'badge' => 'bg-pink-200 text-pink-900'],
            ['bg' => 'bg-lime-50',   'head' => 'bg-lime-100',    'border' => 'border-lime-100',    'title' => 'text-lime-800',   'badge' => 'bg-lime-200 text-lime-900'],
        ];
    @endphp

    {{-- ═══════════════════════ KANBAN BOARD ═══════════════════════ --}}
    <div id="scrollContainer"
        class="flex w-full gap-3 overflow-x-auto pb-3
               lg:h-[calc(100vh-330px)] h-[calc(100vh-390px)]
               scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-transparent">

        {{-- Columna: Sin Tareas --}}
        <div class="flex flex-col flex-shrink-0 w-[272px] bg-blue-50 border border-blue-100 rounded-xl overflow-hidden">
            <div class="flex items-center justify-between px-3 py-2.5 border-b border-blue-100 bg-blue-100">
                <h3 class="text-xs font-semibold text-blue-800 tracking-wide">Sin tareas</h3>
                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-blue-200 text-blue-900">
                    {{ $cntProyecto }}
                </span>
            </div>
            <div class="@if(Auth::user()->isNotColab) task-list tarea-column @endif
                        flex-1 overflow-y-auto space-y-2 p-2">
                {!! $proyectos !!}
            </div>
        </div>

        {{-- Columnas de Tipos de Tarea --}}
        @foreach ($tareaTipo as $idx => $tarea)
            @php $col = $colPalette[$idx % count($colPalette)]; @endphp
            <div class="flex flex-col flex-shrink-0 w-[272px] {{ $col['bg'] }} border {{ $col['border'] }} rounded-xl overflow-hidden">
                <div class="flex items-center justify-between px-3 py-2.5 border-b {{ $col['border'] }} {{ $col['head'] }}">
                    <h3 class="text-xs font-semibold {{ $col['title'] }} tracking-wide truncate">{{ $tarea['nombre_tarea'] }}</h3>
                    <span class="ml-2 flex-shrink-0 text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $col['badge'] }}">
                        {{ $cntTareas[$tarea['id']] ?? 0 }}
                    </span>
                </div>
                <div data-id="{{ $tarea['id'] }}"
                     data-name="{{ $tarea['nombre_tarea'] }}"
                     id="{{ $tarea['id'] }}"
                     class="@if(Auth::user()->isNotColab) task-list @endif
                            flex-1 overflow-y-auto space-y-2 p-2">
                    @if(!empty($arratareas[$tarea['id']]))
                        {!! $arratareas[$tarea['id']] !!}
                    @endif
                </div>
            </div>
        @endforeach

        {{-- Columna: Finalizado --}}
        <div data-id="X" data-name="Finalizado"
            class="@if(Auth::user()->isNotColab) task-end @endif
                   flex flex-col flex-shrink-0 w-[272px] bg-red-50 border border-red-100 rounded-xl overflow-hidden">
            <div class="flex items-center justify-between px-3 py-2.5 border-b border-red-100 bg-red-100">
                <h3 class="text-xs font-semibold text-red-800 tracking-wide">Finalizado</h3>
                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-red-200 text-red-900">0</span>
            </div>
            <div class="flex-1 flex flex-col items-center justify-center p-6 opacity-40">
                <svg class="w-9 h-9 text-red-400 mb-2" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <p class="text-xs text-red-500 font-medium text-center">Arrastra aquí para finalizar</p>
            </div>
        </div>

    </div>

    @include('proyecto.modalAvances')
    @include('proyecto.modalTarea')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="{{ asset('js/tareas/index.js') }}?v={{ filemtime(public_path('js/tareas/index.js')) }}"></script>
@endsection
