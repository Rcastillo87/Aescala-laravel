@extends('layouts.app')
@section('content')
@include('proyecto.filter')
<div class="flex flex-wrap md:flex-nowrap justify-start text-center border-2 p-3 rounded-lg border-gray-200 mb-3">
    <!-- Sección de etiquetas -->
    <div class="flex flex-wrap gap-4 md:gap-2">
        <span class="flex items-center text-center">
            Normal (<=80%)
            <hr class="border-2 bg-green-500 rounded-lg w-[55px] p-[3px] ml-1">
        </span>
        <span class="flex items-center text-center ml-2">
            Próximos a vencer (80-100%)
            <hr class="border-2 bg-orange-400 rounded-lg w-[55px] p-[3px] ml-1">
        </span>

        <span class="flex items-center text-center ml-2">
            Atrasados (=> 100%)
            <hr class="border-2 bg-red-500 rounded-lg w-[55px] p-[3px] ml-1">
        </span>
    </div>
</div>
    @forelse ($items as $item)
        @php
            if(($item->fec_fin_real) && ($item->id_estado == 3)){
               $hoy = $item->fec_fin_real;
            }
            $diasProyec =  $item->dias_trabajo??0;
            $diasTrascuridos =  $item->diasHabilesTrascurridos($hoy, $festivos)??0;

            if ($diasProyec == 0) {
                $porcen = 100;
            } else {
                $porcen = intval(($diasTrascuridos*100)/$diasProyec);
            }
            $bg = match (true) {
                $porcen <= 80 => 'bg-green-500',
                $porcen < 100 => 'bg-orange-400',
                default => 'bg-red-500',
            };
        @endphp
        <div class=" border-2 rounded-lg pb-1 @if ($item->id_estado == 2) bg-green-200 border-green-600 @else border-gray-300 @endif shadow-lg shadow-black-200 mb-2">
            <div class="flex flex-grow mb-2">
                @if ($item->id_estado != 2)
                    <div class="text-center items-center w-[140px] h-[110px] border-2 rounded-xl {{ $bg }} mb-1 ml-3 mt-2 flex flex-col justify-center"
                        data-tooltip-target="tooltip-hover-porcent-{{$item->id}}" data-tooltip-trigger="hover">
                        <p class="text-white text-2xl font-bold">{{$diasTrascuridos}} / {{$diasProyec}}</p>
                        <span class="text-white text-xl font-bold">{{$porcen}}%</span>
                        <small class="text-white hidden xl:flex">F In: {{$item->fecIni}}</small>
                        <small class="text-white hidden xl:flex">F Es: {{$item->fec_fin_est}}</small>
                    </div>
                @endif
                <div id="tooltip-hover-porcent-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                    Dias Habiles VS Duracion Proyecto
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>

                <div class="flex flex-wrap w-full">
                    <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1">
                        <p class="text-lg text-gray-500 font-bold">Nombre Proyecto</p>
                        <span class="text-md text-black">{{$item->nombre_proyecto}}</span>
                    </div>
                    <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                        <p class="text-lg text-gray-500 font-bold">Ubicacion</p>
                        <span class="text-md text-black">{{$departamentos[intval($item['departamento'])]['departamento']}} - 
                            {{$departamentos[intval($item['departamento'])]['ciudades'][$item['ciudad']]}}</span>
                    </div>
                    <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                        <p class="text-lg text-gray-500 font-bold">Dirrecion</p>
                        <span class="text-md text-black">{{$item->direccion}}</span>
                    </div>
                    <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                        <p class="text-lg text-gray-500 font-bold">Contacto Cliente</p>
                        <span class="text-md text-black">{{$item->nombre_cliente}}</span>
                    </div>
                    <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                        <p class="text-lg text-gray-500 font-bold">Telefono Cliente</p>
                        <span class="text-md text-black">{{$item->telefono_cliente}}</span>
                    </div>
                    <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                        <p class="text-lg text-gray-500 font-bold">Total Estim. Proyecto</p>
                        <span class="text-md text-black">{{number_format($item->totalProyecto)}}$</span>
                    </div>
                    <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                        <p class="text-lg text-gray-500 font-bold">Total Ingreso & Egreso</p>
                        <span class="text-md text-black">{{number_format( $item->totalFinanzas )}}$</span>
                    </div>
                    <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                        <p class="text-lg text-gray-500 font-bold">Estado</p>
                        <span class="text-md text-black">{!! $item->span_estado !!}</span>
                    </div>
                    <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                        <p class="text-lg text-gray-500 font-bold">Arquitecto Encargado</p>
                        <span class="text-md text-black">{{$item->user?->nombre_completo}}</span>
                    </div>
                    @if($item->userOB)
                        <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                            <p class="text-lg text-gray-500 font-bold">Cont. Obra Blanca</p>
                            <span class="text-md text-black">{{$item->userOB['nombre_completo']}}</span>
                        </div>
                    @endif
                    @if($item->userCarpi)
                        <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                            <p class="text-lg text-gray-500 font-bold">Cont. Carpinteria</p>
                            <span class="text-md text-black">{{$item->userCarpi['nombre_completo']}}</span>
                        </div>
                    @endif
                    @if($item->fec_fin_real)
                        <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                            <p class="text-lg text-gray-500 font-bold">Fecha de Entrega</p>
                            <span class="text-md text-black">{{ explode(' ', $item->fec_fin_real)[0] }}</span>
                        </div>
                    @endif
                    @if($item->observacion)
                        <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                            <p class="text-lg text-gray-500 font-bold">Observacion</p>
                            <span class="text-md text-black">{{ $item->observacion }}</span>
                        </div>
                    @endif
                    <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                        <p class="text-lg text-gray-500 font-bold">Acepto Tratamiento de Datos Personales</p>
                        <span class="text-md text-black">{!! $item->span_tratadatos !!}</span>
                    </div>

                </div>
                <div class="flex flex-col text-center w-[120px] border-l-2 px-2 mx-2 mt-1">
                    <p class="flex text-gray-500 text-lg font-bold mx-2">Opciones</p>
                    <div class="flex flex-wrap justify-start gap-1 p-1">

                        <!-- Botón Inicio de proyecto -->
                        <div class="relative @if(Auth::user()->isNotColab) @else hidden @endif">
                            <a tabindex="0" data-tooltip-target="tooltip-hover-edit-{{$item->id}}" data-tooltip-trigger="hover" 
                               data-beginProyec='@json($item)' x-on:click="$dispatch('open-modal', 'beginProyec-modal')" x-data="" 
                               class="beginProyec flex items-center justify-center w-10 h-10 text-white bg-cyan-700 hover:bg-white hover:text-cyan-800 border-2 border-cyan-800 focus:ring-4 
                                      focus:outline-none focus:ring-cyan-300 font-medium rounded-full text-sm dark:bg-cyan-600 dark:hover:bg-cyan-700 dark:focus:ring-cyan-800">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                                </svg>
                            </a>
                            <div id="tooltip-hover-edit-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                Editar
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </div>

                        <!-- Botón Contrato -->
                        <div class="relative @if(Auth::user()->isNotColab && $item->entreProyecto->isNotEmpty()) @else hidden @endif">
                            <a tabindex="0" 
                            data-tooltip-target="tooltip-hover-contratoPdf-{{$item->id}}" 
                            data-tooltip-trigger="hover" 
                            href="{{ route('proyecto.contratoPdf', $item->id) }}" 
                            target="_blank"
                            class="flex items-center justify-center w-10 h-10 text-white bg-slate-700 hover:bg-white hover:text-slate-800 border-2 border-slate-800 focus:ring-4 
                                focus:outline-none focus:ring-slate-300 font-medium rounded-full text-sm dark:bg-slate-600 dark:hover:bg-slate-700 dark:focus:ring-slate-800">
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M19 7h1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h11.5M7 14h6m-6 3h6m0-10h.5m-.5 3h.5M7 7h3v3H7V7Z"/>
                                </svg>
                            </a>
                            <div id="tooltip-hover-contratoPdf-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                Contrato
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </div>
                    
                        <!-- Botón Cambio de Estado -->
                        <div class="relative @if(!Auth::user()->isNotColab) hidden @endif">
                            <a tabindex="0" data-tooltip-target="tooltip-hover-{{$item->id}}" data-tooltip-trigger="hover" 
                               onclick="cambiarEstado({{ $item->id }}, {{$item->id_estado}})" 
                               class="flex items-center justify-center w-10 h-10 text-white bg-violet-700 hover:bg-white hover:text-violet-800 border-2 border-violet-800 focus:ring-4 
                                      focus:outline-none focus:ring-violet-300 font-medium rounded-full text-sm dark:bg-violet-600 dark:hover:bg-violet-700 dark:focus:ring-violet-800 cursor-pointer">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 20V7m0 13-4-4m4 4 4-4m4-12v13m0-13 4 4m-4-4-4 4"/>
                                </svg>                    
                            </a>
                            <div id="tooltip-hover-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                Cambio de Estado
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </div>
                    
                        <!-- Botón Ingresos & Egresos -->
                        <div class="relative @if(!Auth::user()->isNotColab) hidden @endif">
                            <a tabindex="0" data-tooltip-target="tooltip-hover-finanza-{{$item->id}}" data-tooltip-trigger="hover"
                               onclick="listFinanzas(0,{{$item->id}})" x-data="" 
                               x-on:click="$dispatch('open-modal', 'finanza-modal')"
                               class="flex items-center justify-center w-10 h-10 text-white bg-yellow-700 hover:bg-white hover:text-yellow-800 border-2 border-yellow-800 focus:ring-4 
                                      focus:outline-none focus:ring-yellow-300 font-medium rounded-full text-sm dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800 cursor-pointer">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.5 21h13M12 21V7m0 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm2-1.8c3.073.661 2.467 2.8 5 2.8M5 8c3.359 0 2.192-2.115 5.012-2.793M7 9.556V7.75m0 1.806-1.95 4.393a.773.773 0 0 0 .37.962.785.785 0 0 0 .362.089h2.436a.785.785 0 0 0 .643-.335.776.776 0 0 0 .09-.716L7 9.556Zm10 0V7.313m0 2.243-1.95 4.393a.773.773 0 0 0 .37.962.786.786 0 0 0 .362.089h2.436a.785.785 0 0 0 .643-.335.775.775 0 0 0 .09-.716L17 9.556Z"/>
                                </svg>       
                            </a>
                            <div id="tooltip-hover-finanza-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                Ingresos & Egresos
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </div>

                        <!-- Botón Cotizacion -->
                        <div class="relative @if(!Auth::user()->isNotColab) hidden @endif">
                            <a tabindex="0" data-tooltip-target="tooltip-hover-cotizacion-{{$item->id}}" data-tooltip-trigger="hover"
                               onclick="listaCotizacion(0,{{$item->id}})" x-data="" 
                               x-on:click="$dispatch('open-modal', 'cotizacion-modal')"
                               class="flex items-center justify-center w-10 h-10 text-white bg-slate-400 hover:bg-white hover:text-slate-500 border-2 border-slate-500 focus:ring-4 
                                      focus:outline-none focus:ring-slate-300 font-medium rounded-full text-sm dark:bg-slate-400 dark:hover:bg-slate-500 dark:focus:ring-slate-500 cursor-pointer">
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 3v4a1 1 0 0 1-1 1H5m4 6 2 2 4-4m4-8v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1Z"/>
                                 </svg>
                            </a>
                            <div id="tooltip-hover-cotizacion-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                Cotizacion
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </div>

                        <!-- Botón despachos -->
                        <div class="relative">
                            <a tabindex="0" data-tooltip-target="tooltip-hover-despachos-{{$item->id}}" data-tooltip-trigger="hover"
                               onclick="listaDespachos({{$item->id}})" x-data="" 
                               x-on:click="$dispatch('open-modal', 'despachos-modal')"
                               class="flex items-center justify-center w-10 h-10 text-white bg-fuchsia-600 hover:bg-white hover:text-fuchsia-500 border-2 border-fuchsia-500 focus:ring-4 
                                      focus:outline-none focus:ring-fuchsia-300 font-medium rounded-full text-sm dark:bg-fuchsia-400 dark:hover:bg-fuchsia-500 dark:focus:ring-fuchsia-500 cursor-pointer">
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7h-1M8 7h-.688M13 5v4m-2-2h4"/>
                                </svg>
                            </a>
                            <div id="tooltip-hover-despachos-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                Despachos
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </div>

                        <!-- Botón comparativo -->
                        <div class="relative @if(!Auth::user()->isNotColab) hidden @endif">
                            <a tabindex="0" data-tooltip-target="tooltip-hover-comparativo-{{$item->id}}" data-tooltip-trigger="hover"
                                onclick="listComparativo({{$item->id}})" x-data="" 
                                x-on:click="$dispatch('open-modal', 'compartivo-modal')"
                                class="flex items-center justify-center w-10 h-10 text-white bg-pink-600 hover:bg-white hover:text-pink-500 border-2 border-pink-500 focus:ring-4 
                                        focus:outline-none focus:ring-pink-300 font-medium rounded-full text-sm dark:bg-pink-400 dark:hover:bg-pink-500 dark:focus:ring-pink-500 cursor-pointer">
                                Vs
                            </a>
                            <div id="tooltip-hover-comparativo-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                Despachos Vs Cotizacion 
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </div>

                        <!-- Botón despachos -->
                        <div class="relative">
                            <a tabindex="0" data-tooltip-target="tooltip-hover-despacho-excel-{{$item->id}}" data-tooltip-trigger="hover"
                               href="{{ route('proyecto.excelDespachoProyecto', $item->id) }}"
                               class="flex items-center justify-center w-10 h-10 text-white bg-green-600 hover:bg-white hover:text-green-500 border-2 border-green-500 focus:ring-4 
                                      focus:outline-none focus:ring-green-300 font-medium rounded-full text-sm dark:bg-green-400 dark:hover:bg-green-500 dark:focus:ring-green-500 cursor-pointer">
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 3v4a1 1 0 0 1-1 1H5m8-2h3m-3 3h3m-4 3v6m4-3H8M19 4v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1ZM8 12v6h8v-6H8Z"/>
                                </svg>
                            </a>
                            <div id="tooltip-hover-despacho-excel-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                Excel de Despachos
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="@if(!Auth::user()->isNotColab) hidden @endif">
                <button type="button" 
                        class="cursor-pointer flex focus:text-blue-600 font-bold hover:text-blue-600 italic items-center justify-between py-2 px-4 text-gray-800 text-left text-sm w-full"
                        data-accordion-target="#tareas_{{ $item->id }}" 
                        aria-expanded="false"
                        aria-controls="tareas_{{ $item->id }}">
                    <div class="flex items-center justify-between text-left w-full">
                        <span class="text-lg">ver tareas</span>
                        <svg data-accordion-icon class="w-5 h-5 shrink-0 transition-transform duration-200 rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                        </svg>
                    </div>
                </button>
                <div id="tareas_{{ $item->id }}" class="hidden px-6 py-3 mx-3 text-lg border-2 border-gray-200 rounded-lg">
                    <div class="flex justify-start text-center">
                        <spam class="text-xl font-bold inline-flex mt-1">
                            <svg class="w-6 h-6 text-gray-800 me-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15v3c0 .5523.44772 1 1 1h4v-4m-5 0v-4m0 4h5m-5-4V6c0-.55228.44772-1 1-1h16c.5523 0 1 .44772 1 1v1.98935M3 11h5v4m9.4708 4.1718-.8696-1.4388-2.8164-.235-2.573-4.2573 1.4873-2.8362 1.4441 2.3893c.3865.6396 1.2183.8447 1.8579.4582.6396-.3866.8447-1.2184.4582-1.858l-1.444-2.38925h3.1353l2.6101 4.27715-1.0713 2.5847.8695 1.4388"/>
                              </svg>
                            Tareas
                        </spam>
                    </div>
                    @forelse($item->tareas as $tarea)
                        <div class="mt-2 flex flex-col gap-2 border-b-[3px] border-gray-300">
                            <div class="transition bg-gradient-to-t hover:from-gray-100 py-3 text-base">
                                @php
                                    if(($tarea->id_tarea_estado == 3) && ($item->fec_fin_real)){
                                        $hoy = $tarea->fec_fin_real;
                                    }
                                    $diasTrascurridosTarea = $tarea->diasHabilesTrascurridos($hoy, $festivos)??0;
                                    $diasTarea = $tarea->dias_trabajo??0;

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
                                @endphp
                                <div class="flex">
                                    <p class="font-semibold text-sm">Tipo Tarea: {{ $tarea->tareaTipo->nombre_tarea }}</p>
                                    <div class="ml-auto flex gap-2 text-sm">
                                        <a class="text-blue-600 hover:text-blue-300 cursor-pointer"
                                            data-tooltip-target="tooltip-hover-avance-{{$tarea->id}}"
                                            onclick="openAvance(0,{{$tarea->id}})"
                                            x-data="" data-tooltip-trigger="hover"
                                            x-on:click="$dispatch('open-modal', 'avance-modal')">
                                            + Avances
                                        </a>
                                        <div id="tooltip-hover-avance-{{$tarea->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 truncate max-w-xs text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                            Avances de la Tarea
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                        <a class="text-blue-600 hover:text-blue-300 cursor-pointer" onclick="editTarea({{$tarea->id}})" x-data=""
                                            x-data data-tooltip-target="tooltip-hover-tarea-{{$tarea->id}}" data-tooltip-trigger="hover">
                                            Editar
                                        </a>
                                        <div id="tooltip-hover-tarea-{{$tarea->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 truncate max-w-xs text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                            Editar la Tarea
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                        {!! $tarea->spanEstado !!}
                                        <span class="font-bold">Dias: {{$diasTrascurridosTarea}} / {{$tarea->dias_trabajo??0}} </span>
                                    </div>
                                </div>

                                <p class="text-sm">Descripcion: {{ $tarea->descripccion }}</p>
                                <div class="my-2 w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700" data-tooltip-target="tooltip-hover-porcenTarea-{{$tarea->id}}" data-tooltip-trigger="hover">
                                    <div class="h-2 rounded-full {{ $bgTarea }}" style="width:{{ ($porcenTarea<100)?$porcenTarea:100 }}%;"></div>
                                </div>
                                <div id="tooltip-hover-porcenTarea-{{$tarea->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 truncate max-w-xs text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                    Porcentage del {{ $porcenTarea }}%
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>

                                <div class="flex justify-between">
                                    <div class="flex items-center text-base">
                                        <small> Encargado: <strong class="text-blue-500">{{ $tarea->user->nombre_completo }}</strong></small>
                                    </div>
                                    <div class="flex text-sm mt-2">
                                        <p class="me-4">Inicio: {{$tarea->fecIni}}</p>
                                        <p>Fin: {{$tarea->fechaFin}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="mt-2 flex text-center justify-center text-red-600 rounded-lg bg-gradient-to-t from-slate-100 p-2">
                            <h3>No hay tareas aún en este proyecto</h3>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    @empty
        <div class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
            No hay registros.
        </div>
    @endforelse
    <!-- Paginador -->
    @if($items->hasPages())
        <div class="mt-4">
            {{ $items->appends(request()->query())->links() }}
        </div>
    @endif

    @include('proyecto.modalAvances')
    @include('proyecto.modalTarea')
    @include('proyecto.modalFinanzas')
    @include('proyecto.modalCotizacion')
    @include('proyecto.modalDespachos')
    @include('proyecto.modalBalance')
    @include('proyecto.modalComparativo')
    @include('proyecto.modalBeginProyec')
@endsection

@section('scripts')
    <script>
        window.tipoDoc = @json($tipoDoc);
        window.estadosProyecto = @json($estado);
        window.departamentos = @json($departamentos);
    </script>
    <script src="{{asset('js/proyecto/index.js')}}"></script>
    <script src="{{ asset('js/pedidos/create.js') }}"></script>
@endsection

