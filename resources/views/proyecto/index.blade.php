@extends('layouts.app')
@section('content')
@include('proyecto.filter')

{{-- ===== LEYENDA + BOTÓN EXCEL ===== --}}
<div class="flex flex-wrap sm:flex-nowrap items-center justify-between gap-3 border border-gray-200 bg-white rounded-xl px-4 py-3 mb-4 shadow-sm">
    <div class="flex flex-wrap gap-x-5 gap-y-2">
        <span class="flex items-center gap-2 text-sm text-gray-600 font-medium">
            <span class="inline-block w-4 h-4 rounded-full bg-green-500 shadow-sm"></span>
            Normal (&lt;=80%)
        </span>
        <span class="flex items-center gap-2 text-sm text-gray-600 font-medium">
            <span class="inline-block w-4 h-4 rounded-full bg-orange-400 shadow-sm"></span>
            Próximos a vencer (80–100%)
        </span>
        <span class="flex items-center gap-2 text-sm text-gray-600 font-medium">
            <span class="inline-block w-4 h-4 rounded-full bg-red-500 shadow-sm"></span>
            Atrasados (&gt;=100%)
        </span>
    </div>

    <div class="@if(!(Auth::user()->isAdmin || Auth::user()->isUser)) hidden @endif shrink-0">
        <button
            data-tooltip-target="tooltip-hover-excel-general"
            data-tooltip-trigger="hover"
            type="button"
            onclick="descargarExcelDespachos()"
            class="inline-flex items-center gap-2 text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 transition-colors duration-150 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 2H8a2 2 0 00-2 2v4H4a2 2 0 00-2 2v10a2 2 0 002 2h11a2 2 0 002-2v-4h2a2 2 0 002-2V4a2 2 0 00-2-2zM4 20V10h11v10H4zm13-6h-2V8a2 2 0 00-2-2H8V4h11v10z"/>
            </svg>
            <span class="hidden sm:inline">Excel General</span>
        </button>
        <div id="tooltip-hover-excel-general" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border bg-white text-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
            Descarga Excel Despachos General
            <div class="tooltip-arrow" data-popper-arrow></div>
        </div>
    </div>
</div>

{{-- ===== LISTA DE PROYECTOS ===== --}}
@forelse ($items as $item)
    @php
        if(($item->fec_fin_real) && ($item->id_estado == 3)){
           $hoy = $item->fec_fin_real;
        }
        $diasProyec        = $item->dias_trabajo ?? 0;
        $diasTrascuridos   = $item->diasHabilesTrascurridos($hoy, $festivos) ?? 0;

        if($diasProyec <= $diasTrascuridos){
            $porcen = 100;
        } else {
            $porcen = intval(($diasTrascuridos * 100) / $diasProyec);
        }

        $bg = match (true) {
            $porcen <= 80  => 'bg-green-500',
            $porcen < 100  => 'bg-orange-400',
            default        => 'bg-red-500',
        };

        $bgLight = match (true) {
            $porcen <= 80  => 'bg-green-50 border-green-200',
            $porcen < 100  => 'bg-orange-50 border-orange-200',
            default        => 'bg-red-50 border-red-200',
        };

        $porcenTarea = 0;
        if($item->maxTarea){
            foreach ($tareatipo as $key => $value) {
                if($item->maxTarea <= $value['orden']){
                    $porcenTarea += $value['porcentage'];
                }
            }
        }

    @endphp

    <div class="group bg-white border rounded-xl mb-3 shadow-sm hover:shadow-md transition-shadow duration-200
        @if($item->id_estado == 2) border-green-400 ring-1 ring-green-300 @else border-gray-200 @endif">

        {{-- ===== CABECERA DEL CARD ===== --}}
        <div class="flex flex-col sm:flex-row gap-3 p-4">

            {{-- Indicador de progreso (solo si no está terminado) --}}
            @if ($item->id_estado != 2)
                <div class="shrink-0 self-start">
                    <div class="relative flex flex-col items-center justify-center w-28 sm:w-32 rounded-xl {{ $bg }} p-3 text-white shadow-sm cursor-default"
                         data-tooltip-target="tooltip-hover-porcent-{{$item->id}}"
                         data-tooltip-trigger="hover">
                        <span class="text-2xl font-extrabold leading-none tracking-tight">{{ $porcenTarea }}%</span>
                        <span class="text-xs font-semibold mt-1 opacity-90">{{ $diasTrascuridos }} / {{ $diasProyec }} días</span>
                        <div class="mt-2 w-full bg-white/30 rounded-full h-1.5">
                            <div class="bg-white rounded-full h-1.5 transition-all duration-500"
                                 style="width:{{ min($porcen, 100) }}%"></div>
                        </div>
                        <div class="mt-2 text-[10px] opacity-80 space-y-0.5 w-full hidden xl:block">
                            <div class="flex justify-between"><span>Inicio:</span><span>{{ $item->fecIni ?? '--' }}</span></div>
                            <div class="flex justify-between"><span>Fin:</span><span>{{ $item->fec_fin_est ?? '--' }}</span></div>
                            <div class="flex justify-between"><span>Comis:</span><span>{{ $item->fecha_comision ?? '--' }}</span></div>
                        </div>
                    </div>
                    <div id="tooltip-hover-porcent-{{$item->id}}" role="tooltip"
                         class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border bg-white text-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                        Días Hábiles vs Duración Proyecto
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                </div>
            @endif

            {{-- ===== CAMPOS DE INFORMACIÓN ===== --}}
            <div class="flex-1 min-w-0">
                {{-- Nombre del proyecto destacado --}}
                <div class="mb-3 pb-2 border-b border-gray-100">
                    <h3 class="text-base sm:text-lg font-bold text-gray-800 leading-tight truncate">
                        {{ $item->nombre_proyecto }}
                    </h3>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        {!! $item->span_estado !!}
                    </div>
                </div>

                {{-- Grid de datos --}}
                <div class="grid grid-cols-1 xs:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-x-4 gap-y-3">

                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Ubicación</p>
                        <p class="text-sm text-gray-700 leading-snug">
                            {{ $departamentos[intval($item['departamento'])]['departamento'] }} –
                            {{ $departamentos[intval($item['departamento'])]['ciudades'][$item['ciudad']] }} –
                            {{ $item->ubicacion !== null ? ($ubicacion[$item->ubicacion] ?? 'N/A') : 'N/A' }}
                        </p>
                    </div>

                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Dirección</p>
                        <p class="text-sm text-gray-700 leading-snug truncate">{{ $item->direccion }}</p>
                    </div>

                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Contacto Cliente</p>
                        <p class="text-sm text-gray-700 leading-snug">{{ $item->nombre_cliente }}</p>
                    </div>

                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Teléfono</p>
                        <p class="text-sm text-gray-700 leading-snug font-mono">{{ $item->telefono_cliente }}</p>
                    </div>

                    @if($item->user)
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Residente</p>
                            <p class="text-sm text-gray-700 leading-snug">{{ $item->user?->nombre_completo }}</p>
                        </div>
                    @endif

                    @if($item->userOB)
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Cont. Obra Blanca</p>
                            <p class="text-sm text-gray-700 leading-snug">{{ $item->userOB['nombre_completo'] }}</p>
                        </div>
                    @endif

                    @if($item->userCarpi)
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Cont. Carpintería</p>
                            <p class="text-sm text-gray-700 leading-snug">{{ $item->userCarpi['nombre_completo'] }}</p>
                        </div>
                    @endif

                    @if($item->userDiseno)
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Diseñador</p>
                            <p class="text-sm text-gray-700 leading-snug">{{ $item->userDiseno['nombre_completo'] }}</p>
                        </div>
                    @endif

                    @if($item->fec_fin_real)
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Fecha de Entrega</p>
                            <p class="text-sm text-gray-700 leading-snug font-medium">{{ explode(' ', $item->fec_fin_real)[0] }}</p>
                        </div>
                    @endif

                    @if($item->paz_salvo == 1)
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Paz & salvo</p>
                            <p class="text-sm text-gray-700 leading-snug font-medium">{!! $item->apaz !!}</p>
                        </div>
                    @endif

                    @if($item->observacion)
                        <div class="min-w-0 xs:col-span-2 md:col-span-3 xl:col-span-4">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Observación</p>
                            <p class="text-sm text-gray-600 leading-snug italic">{{ $item->observacion }}</p>
                        </div>
                    @endif

                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Trat. Datos Personales</p>
                        <p class="text-sm">{!! $item->span_tratadatos !!}</p>
                    </div>

                </div>{{-- /grid --}}
            </div>{{-- /info --}}

            {{-- ===== ACCIONES =====
            <div class="shrink-0 flex sm:flex-col items-start gap-1.5 sm:border-l sm:pl-3 sm:ml-1 border-t sm:border-t-0 pt-3 sm:pt-0 flex-wrap">--}}
            <div class="shrink-0 grid grid-cols-2 gap-2 sm:border-l sm:pl-3 sm:ml-1 border-t sm:border-t-0 pt-3 sm:pt-0">
                {{-- <p class="w-full text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5 hidden sm:block">Opciones</p>--}}
                <p class="col-span-2 text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5 hidden sm:block">
                    Opciones
                </p>

                {{-- Editar --}}
                <div class="relative @if(!(Auth::user()->isAdmin || Auth::user()->isUser)) hidden @endif">
                    <a tabindex="0"
                       data-tooltip-target="tooltip-hover-edit-{{$item->id}}"
                       data-tooltip-trigger="hover"
                       data-beginProyec='@json($item)'
                       x-on:click="$dispatch('open-modal', 'beginProyec-modal')"
                       x-data=""
                       class="beginProyec flex items-center justify-center w-9 h-9 rounded-lg text-white bg-cyan-600 hover:bg-cyan-700 border border-cyan-700 focus:ring-2 focus:ring-cyan-300 transition-colors duration-150 cursor-pointer shadow-sm">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                        </svg>
                    </a>
                    <div id="tooltip-hover-edit-{{$item->id}}" role="tooltip"
                         class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border bg-white text-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                        Editar
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                </div>

                {{-- Contrato --}}
                <div class="relative
                    @if(!(Auth::user()->isAdmin || Auth::user()->isUser || Auth::user()->isColab || Auth::user()->isComer || Auth::user()->isAlmacenista || Auth::user()->isDiseno) || ($item->entreProyecto->count() === 0))
                        hidden
                    @endif">
                    <a tabindex="0"
                       data-tooltip-target="tooltip-hover-contratoPdf-{{$item->id}}"
                       data-tooltip-trigger="hover"
                       href="{{ route('proyecto.contratoPdf', $item->id) }}"
                       target="_blank"
                       class="flex items-center justify-center w-9 h-9 rounded-lg text-white bg-slate-600 hover:bg-slate-700 border border-slate-700 focus:ring-2 focus:ring-slate-300 transition-colors duration-150 shadow-sm">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7h1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h11.5M7 14h6m-6 3h6m0-10h.5m-.5 3h.5M7 7h3v3H7V7Z"/>
                        </svg>
                    </a>
                    <div id="tooltip-hover-contratoPdf-{{$item->id}}" role="tooltip"
                         class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border bg-white text-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                        Contrato
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                </div>

                {{-- Tratamiento de Datos --}}
                <div class="relative @if($item->acepta_trata_datos == 0 || !(Auth::user()->isAdmin || Auth::user()->isUser)) hidden @endif">
                    <a tabindex="0"
                       data-tooltip-target="tooltip-hover-trataDatosPDF-{{$item->id}}"
                       data-tooltip-trigger="hover"
                       href="{{ route('proyecto.trataDatosPDF', $item->id) }}"
                       target="_blank"
                       class="flex items-center justify-center w-9 h-9 rounded-lg text-white bg-orange-600 hover:bg-orange-700 border border-orange-700 focus:ring-2 focus:ring-orange-300 transition-colors duration-150 shadow-sm">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="square" stroke-linejoin="round" stroke-width="2" d="M7 19H5a1 1 0 0 1-1-1v-1a3 3 0 0 1 3-3h1m4-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm7.441 1.559a1.907 1.907 0 0 1 0 2.698l-6.069 6.069L10 19l.674-3.372 6.07-6.07a1.907 1.907 0 0 1 2.697 0Z"/>
                        </svg>
                    </a>
                    <div id="tooltip-hover-trataDatosPDF-{{$item->id}}" role="tooltip"
                         class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border bg-white text-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                        Tratamiento de Datos
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                </div>

                {{-- Cambio de Estado --}}
                <div class="relative @if(!(Auth::user()->isAdmin || Auth::user()->isUser || Auth::user()->isComer || Auth::user()->isAlmacenista)) hidden @endif">
                    <a tabindex="0"
                       data-tooltip-target="tooltip-hover-{{$item->id}}"
                       data-tooltip-trigger="hover"
                       onclick="cambiarEstado({{ $item->id }}, {{$item->id_estado}})"
                       class="flex items-center justify-center w-9 h-9 rounded-lg text-white bg-violet-600 hover:bg-violet-700 border border-violet-700 focus:ring-2 focus:ring-violet-300 transition-colors duration-150 cursor-pointer shadow-sm">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 20V7m0 13-4-4m4 4 4-4m4-12v13m0-13 4 4m-4-4-4 4"/>
                        </svg>
                    </a>
                    <div id="tooltip-hover-{{$item->id}}" role="tooltip"
                         class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border bg-white text-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                        Cambio de Estado
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                </div>

                {{-- Cartera --}}
                <div class="relative @if(!(Auth::user()->isAdmin || Auth::user()->isUser) || ($item->entreProyecto->count() === 0)) hidden @endif">
                    <a tabindex="0"
                       data-tooltip-target="tooltip-hover-cartera-{{$item->id}}"
                       data-tooltip-trigger="hover"
                       href="{{ Route('cartera.index', $item->id) }}"
                       class="flex items-center justify-center w-9 h-9 rounded-lg text-white bg-slate-400 hover:bg-slate-500 border border-slate-500 focus:ring-2 focus:ring-slate-300 transition-colors duration-150 cursor-pointer shadow-sm">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8H5m12 0a1 1 0 0 1 1 1v2.6M17 8l-4-4M5 8a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.6M5 8l4-4 4 4m6 4h-4a2 2 0 1 0 0 4h4a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1Z"/>
                        </svg>
                    </a>
                    <div id="tooltip-hover-cartera-{{$item->id}}" role="tooltip"
                         class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border bg-white text-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                        Cartera
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                </div>

                {{-- Calendario --}}
                <div class="relative @if(!(Auth::user()->isAdmin || Auth::user()->isUser) || !$item->fec_inicio) hidden @endif">
                    <a tabindex="0"
                       data-tooltip-target="tooltip-hover-calendario-{{$item->id}}"
                       data-tooltip-trigger="hover"
                       onclick="abrirCalendarioProy({{ $item->id }})"
                       class="flex items-center justify-center w-9 h-9 rounded-lg text-white bg-yellow-600 hover:bg-yellow-700 border border-yellow-700 focus:ring-2 focus:ring-yellow-300 transition-colors duration-150 cursor-pointer shadow-sm">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 10h16m-8-3V4M7 7V4m10 3V4M5 20h14a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Zm3-7h.01v.01H8V13Zm4 0h.01v.01H12V13Zm4 0h.01v.01H16V13Zm-8 4h.01v.01H8V17Zm4 0h.01v.01H12V17Zm4 0h.01v.01H16V17Z"/>
                        </svg>
                    </a>
                    <div id="tooltip-hover-calendario-{{$item->id}}" role="tooltip"
                         class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border bg-white text-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                        Calendario
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                </div>

                {{-- Despachos --}}
                <div class="relative {{ !Auth::user()->isDiseno ? '' : 'hidden' }}">
                    <a tabindex="0"
                       data-tooltip-target="tooltip-hover-despachos-{{$item->id}}"
                       data-tooltip-trigger="hover"
                       onclick="listaDespachos({{$item->id}})"
                       x-data=""
                       x-on:click="$dispatch('open-modal', 'despachos-modal')"
                       class="flex items-center justify-center w-9 h-9 rounded-lg text-white bg-fuchsia-600 hover:bg-fuchsia-700 border border-fuchsia-700 focus:ring-2 focus:ring-fuchsia-300 transition-colors duration-150 cursor-pointer shadow-sm">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7h-1M8 7h-.688M13 5v4m-2-2h4"/>
                        </svg>
                    </a>
                    <div id="tooltip-hover-despachos-{{$item->id}}" role="tooltip"
                         class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border bg-white text-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                        Despachos
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                </div>

                {{-- Excel Despachos --}}
                <div class="relative @if(!(Auth::user()->isAdmin || Auth::user()->isUser)) hidden @endif">
                    <a tabindex="0"
                       data-tooltip-target="tooltip-hover-despacho-excel-{{$item->id}}"
                       data-tooltip-trigger="hover"
                       onclick="descargarExcelDespachos({{ $item->id }})"
                       class="flex items-center justify-center w-9 h-9 rounded-lg text-white bg-green-600 hover:bg-green-700 border border-green-700 focus:ring-2 focus:ring-green-300 transition-colors duration-150 cursor-pointer shadow-sm">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 3v4a1 1 0 0 1-1 1H5m8-2h3m-3 3h3m-4 3v6m4-3H8M19 4v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1ZM8 12v6h8v-6H8Z"/>
                        </svg>
                    </a>
                    <div id="tooltip-hover-despacho-excel-{{$item->id}}" role="tooltip"
                         class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border bg-white text-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                        Excel de Despachos
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                </div>

            </div>{{-- /acciones --}}
        </div>{{-- /cabecera --}}

        {{-- ===== ACORDEÓN DE TAREAS ===== --}}
        <div class="@if(!Auth::user()->isNotColab) hidden @endif border-t border-gray-100">
            <button type="button"
                    class="cursor-pointer flex items-center justify-between w-full px-4 py-2.5 text-sm font-semibold text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-colors duration-150 rounded-b-xl"
                    data-accordion-target="#tareas_{{ $item->id }}"
                    aria-expanded="false"
                    aria-controls="tareas_{{ $item->id }}">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15v3c0 .5523.44772 1 1 1h4v-4m-5 0v-4m0 4h5m-5-4V6c0-.55228.44772-1 1-1h16c.5523 0 1 .44772 1 1v1.98935M3 11h5v4m9.4708 4.1718-.8696-1.4388-2.8164-.235-2.573-4.2573 1.4873-2.8362 1.4441 2.3893c.3865.6396 1.2183.8447 1.8579.4582.6396-.3866.8447-1.2184.4582-1.858l-1.444-2.38925h3.1353l2.6101 4.27715-1.0713 2.5847.8695 1.4388"/>
                    </svg>
                    Ver Tareas
                    <span class="text-xs bg-gray-100 text-gray-500 rounded-full px-2 py-0.5 font-normal">
                        {{ $item->tareas->count() }}
                    </span>
                </span>
                <svg data-accordion-icon class="w-4 h-4 shrink-0 transition-transform duration-200 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                </svg>
            </button>

            <div id="tareas_{{ $item->id }}" class="hidden px-4 pb-4">
                <div class="space-y-2 mt-1">
                    @forelse($item->tareas as $tarea)
                        @php
                            if(($tarea->id_tarea_estado == 3) && ($item->fec_fin_real)){
                                $hoy = $tarea->fec_fin_real;
                            }
                            $diasTrascurridosTarea = $tarea->diasHabilesTrascurridos($hoy, $festivos) ?? 0;
                            $diasTarea = $tarea->dias_trabajo ?? 0;

                            if ($diasTarea == 0) {
                                $porcenTarea = 100;
                            } else {
                                $porcenTarea = intval(($diasTrascurridosTarea * 100) / ((int)$diasTarea));
                            }

                            $bgTarea = match (true) {
                                $porcenTarea <= 80  => 'bg-green-500',
                                $porcenTarea < 100  => 'bg-orange-400',
                                default             => 'bg-red-500',
                            };
                        @endphp

                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 hover:bg-white transition-colors duration-150">
                            {{-- Cabecera tarea --}}
                            <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                                <p class="text-sm font-semibold text-gray-700">
                                    {{ $tarea->tareaTipo->nombre_tarea }}
                                </p>
                                <div class="flex flex-wrap items-center gap-2">
                                    <a class="text-xs text-blue-600 hover:text-blue-400 cursor-pointer font-medium
                                              @if(!(Auth::user()->isAdmin || Auth::user()->isUser)) hidden @endif"
                                       data-tooltip-target="tooltip-hover-avance-{{$tarea->id}}"
                                       onclick="openAvance(0,{{$tarea->id}})"
                                       x-data=""
                                       data-tooltip-trigger="hover"
                                       x-on:click="$dispatch('open-modal', 'avance-modal')">
                                        + Avances
                                    </a>
                                    <div id="tooltip-hover-avance-{{$tarea->id}}" role="tooltip"
                                         class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border bg-white text-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                                        Avances de la Tarea
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                    <a class="text-xs text-blue-600 hover:text-blue-400 cursor-pointer font-medium
                                              @if(!(Auth::user()->isAdmin || Auth::user()->isUser)) hidden @endif"
                                       onclick="editTarea({{$tarea->id}})"
                                       x-data
                                       data-tooltip-target="tooltip-hover-tarea-{{$tarea->id}}"
                                       data-tooltip-trigger="hover">
                                        Editar
                                    </a>
                                    <div id="tooltip-hover-tarea-{{$tarea->id}}" role="tooltip"
                                         class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border bg-white text-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                                        Editar la Tarea
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                    {!! $tarea->spanEstado !!}
                                    <span class="text-xs font-bold text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full">
                                        {{ $diasTrascurridosTarea }} / {{ $tarea->dias_trabajo ?? 0 }} días
                                    </span>
                                </div>
                            </div>

                            {{-- Descripción --}}
                            @if($tarea->descripccion)
                                <p class="text-xs text-gray-500 mb-2">{{ $tarea->descripccion }}</p>
                            @endif

                            {{-- Barra de progreso --}}
                            <div class="w-full bg-gray-200 rounded-full h-1.5 mb-2 cursor-default"
                                 data-tooltip-target="tooltip-hover-porcenTarea-{{$tarea->id}}"
                                 data-tooltip-trigger="hover">
                                <div class="h-1.5 rounded-full {{ $bgTarea }} transition-all duration-500"
                                     style="width:{{ ($porcenTarea < 100) ? $porcenTarea : 100 }}%;"></div>
                            </div>
                            <div id="tooltip-hover-porcenTarea-{{$tarea->id}}" role="tooltip"
                                 class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border bg-white text-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                                Porcentaje del {{ $porcenTarea }}%
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>

                            {{-- Encargado y fechas --}}
                            <div class="flex flex-wrap justify-between items-center gap-2 text-xs text-gray-500">
                                <span>
                                    Encargado: <strong class="text-blue-500">{{ $tarea->user->nombre_completo }}</strong>
                                </span>
                                <span class="flex gap-3">
                                    <span>Inicio: {{ $tarea->fecIni }}</span>
                                    <span>Fin: {{ $tarea->fechaFin }}</span>
                                </span>
                            </div>
                        </div>

                    @empty
                        <div class="flex items-center justify-center gap-2 py-4 text-sm text-red-500 bg-red-50 border border-red-100 rounded-lg">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            No hay tareas aún en este proyecto
                        </div>
                    @endforelse
                </div>
            </div>
        </div>{{-- /tareas --}}

    </div>{{-- /card proyecto --}}

@empty
    <div class="flex items-center justify-center py-12 text-gray-400">
        <svg class="w-6 h-6 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
        </svg>
        <span class="text-sm font-medium">No hay registros.</span>
    </div>
@endforelse

{{-- Paginador --}}
@if($items->hasPages())
    <div class="mt-4">
        {{ $items->links() }}
    </div>
@endif

@include('proyecto.modalAvances')
@include('proyecto.modalTarea')
@include('proyecto.modalCalendario')
@include('proyecto.modalCotizacion')
@include('proyecto.modalDespachos')
@include('proyecto.modalBalance')
@include('proyecto.modalComparativo')
@include('proyecto.modalBeginProyec')
@endsection

@section('scripts')
    @if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                title: 'Error de validación',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                icon: 'error',
                confirmButtonText: 'Entendido'
            });
        });
    </script>
    @endif

    <script>
        window.tipoDoc         = @json($tipoDoc);
        window.estadosProyecto = @json($estado);
        window.departamentos   = @json($departamentos);
    </script>
    <script src="{{ asset('js/proyecto/index.js') }}?v={{ filemtime(public_path('js/proyecto/index.js')) }}"></script>
    <script src="{{ asset('js/pedidos/create.js') }}?v={{ filemtime(public_path('js/pedidos/create.js')) }}"></script>
@endsection
