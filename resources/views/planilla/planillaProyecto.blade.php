@extends('layouts.app')
@section('content')
    <div class="mx-2">
        <div class="flex-1 min-w-0">
            <div class="py-1 mb-1 border-b border-gray-100 flex items-center justify-between">

                <div class="">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Proyecto</span>
                    <h3 class="text-base sm:text-lg font-bold text-gray-800 leading-tight truncate">
                        {{ $proyecto->nombre_proyecto }}
                    </h3>
                </div>

                <div class="">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Estado</span>
                    <div class="flex flex-wrap items-center">
                        {!! $proyecto->span_estado !!}
                    </div>
                </div>

            </div>
 
            {{-- Grid de datos --}}
            <div class="grid grid-cols-1 xs:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-x-4 gap-y-3">

                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Ubicación</p>
                    <p class="text-sm text-gray-700 leading-snug">
                        {{ $departamentos[intval($proyecto['departamento'])]['departamento'] }} –
                        {{ $departamentos[intval($proyecto['departamento'])]['ciudades'][$proyecto['ciudad']] }} –
                        {{ $proyecto->ubicacion !== null ? ($ubicacion[$proyecto->ubicacion] ?? 'N/A') : 'N/A' }}
                    </p>
                </div>

                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Dirección</p>
                    <p class="text-sm text-gray-700 leading-snug truncate">{{ $proyecto->direccion }}</p>
                </div>

                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Contacto Cliente</p>
                    <p class="text-sm text-gray-700 leading-snug">{{ $proyecto->nombre_cliente }}</p>
                </div>

                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Teléfono</p>
                    <p class="text-sm text-gray-700 leading-snug font-mono">{{ $proyecto->telefono_cliente }}</p>
                </div>

                @if($proyecto->user)
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Residente</p>
                        <p class="text-sm text-gray-700 leading-snug">{{ $proyecto->user?->nombre_completo }}</p>
                    </div>
                @endif

                @if($proyecto->userOB)
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Cont. Obra Blanca</p>
                        <p class="text-sm text-gray-700 leading-snug">{{ $proyecto->userOB['nombre_completo'] }}</p>
                    </div>
                @endif

                @if($proyecto->user_carpinteria)
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Cont. Carpintería</p>
                        <p class="text-sm text-gray-700 leading-snug">{{ $proyecto->user_carpinteria }}</p>
                    </div>
                @endif

                @if($proyecto->userDiseno)
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Diseñador</p>
                        <p class="text-sm text-gray-700 leading-snug">{{ $proyecto->userDiseno['nombre_completo'] }}</p>
                    </div>
                @endif

                @if($proyecto->fec_fin_real)
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Fecha de Entrega</p>
                        <p class="text-sm text-gray-700 leading-snug font-medium">{{ explode(' ', $proyecto->fec_fin_real)[0] }}</p>
                    </div>
                @endif

                @if($proyecto->paz_salvo == 1)
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Paz & salvo</p>
                        <p class="text-sm text-gray-700 leading-snug font-medium">{!! $proyecto->apaz !!}</p>
                    </div>
                @endif

                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Area Privada</p>
                    <p class="text-sm text-gray-700 leading-snug font-medium">{{ $proyecto->area_privada }} mt²</p>
                </div>

                @if($proyecto->observacion)
                    <div class="min-w-0 xs:col-span-2 md:col-span-3 xl:col-span-4">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Observación</p>
                        <p class="text-sm text-gray-600 leading-snug italic">{{ $proyecto->observacion }}</p>
                    </div>
                @endif

            </div>
        </div>

        <hr class="my-4 border-gray-200">

        {{-- Botón --}}
        <div class="flex justify-start w-full">
            <x-secondary-button class="flex items-center gap-2 px-4 py-2 cursor-pointer" id="btn-open-modal"
                x-on:click="$dispatch('open-modal', 'modalPlanillaEntregable-modal')"
                x-data=""
            >
                <b>+</b>
                <span>Agregar Entregable</span>
            </x-secondary-button>
        </div>

        <div id="entregables-container" class="py-2 justify-start w-full space-y-2 @if(empty($planillaEntregables)) hidden @else  @endif">
            <h2 class="text-xl font-bold text-[#242e68]">Entregables Agregados</h2>
            <form id="savePlantilla" action="{{ route('planilla.savePlantilla') }}" method="POST" class="w-full items-center">
                <input type="hidden" name="id_proyecto" value="{{ $proyecto->id }}">
                <div id="area-div" class="mt-2 space-y-2 w-full"></div>
                <button type="submit" class="mt-2 px-2 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Guardar Entregables 
                </button>
            </form>
        </div>

        <hr class="my-2 border-gray-200">

        <div class="">

            <div class="flex items-center gap-2 mb-4">
                <h2 class="text-xl font-bold text-[#242e68]">Configuración del Proyecto</h2>
                <button type="button" data-tooltip-target="tooltip-config-general"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zM9 8a1 1 0 112 0v5a1 1 0 11-2 0V8zm1-4a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div id="tooltip-config-general" role="tooltip"
                    class="absolute z-10 invisible inline-block w-72 px-3 py-2 text-sm text-white bg-gray-900 rounded-lg shadow-xs opacity-0 tooltip">
                    Estos valores se usan para calcular automáticamente el costo del proyecto según su área.
                    Se sugieren según el área registrada, pero puedes aceptarlos o eliminarlos cuando quieras.
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                {{-- ============================= --}}
                {{-- TIPO 1: Valor área Proyecto --}}
                {{-- ============================= --}}
                <div class="config-block bg-white border rounded-2xl shadow-md p-5"
                    data-tipo="1" data-id-proyecto="{{ $proyecto->id }}" data-kind="simple">

                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-md font-semibold text-[#242e68]">Valor Área Proyecto</h3>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            Tipo 1
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mb-3">
                        Costo por m² calculado según el área privada del proyecto ({{ $areaProyecto ?? $proyecto->area_privada }} m²).
                    </p>

                    {{-- ESTADO: GUARDADO --}}
                    <div class="state-saved {{ $configProyecto->has(1) ? '' : 'hidden' }}">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex items-center justify-between">
                            <div>
                                <span class="text-xs text-green-700 font-medium block">Configuración aceptada</span>
                                <span class="saved-value text-lg font-bold text-green-800">
                                    {{ $configProyecto->has(1) ? '$ ' . number_format($configProyecto[1]->valor, 0, ',', '.') : '' }}
                                </span>
                            </div>
                            <button type="button" class="btn-delete-config text-red-600 hover:bg-red-100 p-2 rounded-lg" data-tipo="1">
                                🗑️
                            </button>
                        </div>
                    </div>

                    {{-- ESTADO: SUGERIDO --}}
                    <div class="state-suggested {{ $configProyecto->has(1) ? 'hidden' : '' }}">
                        @if($dataValorArea)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <span class="text-xs text-yellow-700 font-medium block mb-1">Valor sugerido</span>
                                <span class="suggested-value text-lg font-bold text-yellow-800 block mb-2">
                                    $ {{ number_format($dataValorArea->valor_intervalo, 0, ',', '.') }}
                                </span>
                                <p class="text-xs text-gray-500 mb-2">
                                    Rango: {{ $dataValorArea->area_min }} m² - {{ $dataValorArea->area_max }} m² (año {{ $dataValorArea->año }})
                                </p>
                                <button type="button"
                                    class="btn-accept-config w-full bg-[#242e68] hover:bg-[#1a2150] text-white text-sm font-medium rounded-lg px-3 py-2"
                                    data-tipo="1" data-value="{{ $dataValorArea->valor_intervalo }}">
                                    Aceptar configuración
                                </button>
                            </div>
                        @else
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-center">
                                <span class="text-sm text-gray-500">No hay valor configurado para esta área.</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ============================= --}}
                {{-- TIPO 2: Valor área Enchape --}}
                {{-- ============================= --}}
                <div class="config-block bg-white border rounded-2xl shadow-md p-5"
                    data-tipo="2" data-id-proyecto="{{ $proyecto->id }}" data-kind="simple">

                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-md font-semibold text-[#242e68]">Valor Área Enchape</h3>
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            Tipo 2
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mb-3">
                        Costo por m² de enchape según el área privada del proyecto.
                    </p>

                    <div class="state-saved {{ $configProyecto->has(2) ? '' : 'hidden' }}">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex items-center justify-between">
                            <div>
                                <span class="text-xs text-green-700 font-medium block">Configuración aceptada</span>
                                <span class="saved-value text-lg font-bold text-green-800">
                                    {{ $configProyecto->has(2) ? '$ ' . number_format($configProyecto[2]->valor, 0, ',', '.') : '' }}
                                </span>
                            </div>
                            <button type="button" class="btn-delete-config text-red-600 hover:bg-red-100 p-2 rounded-lg" data-tipo="2">
                                🗑️
                            </button>
                        </div>
                    </div>

                    <div class="state-suggested {{ $configProyecto->has(2) ? 'hidden' : '' }}">
                        @if($dataValorAreaEnchape)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <span class="text-xs text-yellow-700 font-medium block mb-1">Valor sugerido</span>
                                <span class="suggested-value text-lg font-bold text-yellow-800 block mb-2">
                                    $ {{ number_format($dataValorAreaEnchape->valor_intervalo, 0, ',', '.') }}
                                </span>
                                <p class="text-xs text-gray-500 mb-2">
                                    Rango: {{ $dataValorAreaEnchape->area_min }} m² - {{ $dataValorAreaEnchape->area_max }} m² (año {{ $dataValorAreaEnchape->año }})
                                </p>
                                <button type="button"
                                    class="btn-accept-config w-full bg-[#242e68] hover:bg-[#1a2150] text-white text-sm font-medium rounded-lg px-3 py-2"
                                    data-tipo="2" data-value="{{ $dataValorAreaEnchape->valor_intervalo }}">
                                    Aceptar configuración
                                </button>
                            </div>
                        @else
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-center">
                                <span class="text-sm text-gray-500">No hay valor configurado para esta área.</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ================================== --}}
                {{-- TIPO 3: Porcentajes del Proyecto --}}
                {{-- ================================== --}}
                <div class="config-block bg-white border rounded-2xl shadow-md p-5"
                    data-tipo="3" data-id-proyecto="{{ $proyecto->id }}" data-kind="multi">

                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-md font-semibold text-[#242e68]">Porcentajes del Proyecto</h3>
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            Tipo 3
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mb-3">
                        Porcentajes aplicados sobre el proyecto (administración, imprevistos, utilidad, etc.).
                    </p>

                    {{-- ESTADO: GUARDADO --}}
                    <div class="state-saved {{ $configProyecto->has(3) ? '' : 'hidden' }}">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <span class="text-xs text-green-700 font-medium block mb-2">Configuración aceptada</span>
                            <ul class="saved-list space-y-1 mb-2">
                                @if($configProyecto->has(3))
                                    @foreach($configProyecto[3]->valor as $item)
                                        <li class="flex justify-between text-sm">
                                            <span class="text-gray-700">{{ $item['concepto'] }}</span>
                                            <span class="font-semibold text-green-800">{{ $item['porcentage'] }}%</span>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                            <button type="button" class="btn-delete-config w-full text-red-600 hover:bg-red-100 text-sm font-medium rounded-lg px-3 py-2 border border-red-200" data-tipo="3">
                                🗑️ Eliminar configuración
                            </button>
                        </div>
                    </div>

                    {{-- ESTADO: SUGERIDO --}}
                    <div class="state-suggested {{ $configProyecto->has(3) ? 'hidden' : '' }}">
                        @if($dataConfigPorcentajes && $dataConfigPorcentajes->count())
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <span class="text-xs text-yellow-700 font-medium block mb-2">Porcentajes sugeridos ({{ $dataConfigPorcentajes->first()->año }})</span>
                                <ul class="space-y-1 mb-3">
                                    @foreach($dataConfigPorcentajes as $c)
                                        <li class="flex justify-between text-sm">
                                            <span class="text-gray-700">{{ $c->concepto }}</span>
                                            <span class="font-semibold text-yellow-800">{{ $c->porcentage }}%</span>
                                        </li>
                                    @endforeach
                                </ul>
                                <button type="button"
                                    class="btn-accept-config w-full bg-[#242e68] hover:bg-[#1a2150] text-white text-sm font-medium rounded-lg px-3 py-2"
                                    data-tipo="3"
                                    data-conceptos="{{ $dataConfigPorcentajes->map(fn($c) => ['concepto' => $c->concepto, 'porcentage' => $c->porcentage])->toJson() }}">
                                    Aceptar configuración
                                </button>
                            </div>
                        @else
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-center">
                                <span class="text-sm text-gray-500">No hay porcentajes configurados.</span>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Form oculto para ACEPTAR configuración --}}
            <form id="formAcceptConfig" action="{{ route('planilla.saveConfigPlantilla') }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="id_proyecto" value="{{ $proyecto->id }}">
                <input type="hidden" name="tipo" id="accept-tipo">
                <input type="hidden" name="valor_config" id="accept-valor">
                <input type="hidden" name="conceptos" id="accept-conceptos">
                <button type="submit"></button>
            </form>

            {{-- Form oculto para ELIMINAR configuración --}}
            <form id="formDeleteConfig"
                action="#"
                data-base-url="{{ url('/planilla/deleteConfigPlantilla/' . $proyecto->id) }}"
                method="POST"
                class="hidden">
                @csrf
                @method('DELETE')
                <button type="submit"></button>
            </form>

        </div>

        <hr class="my-4 border-gray-200">

        <div class=" lg:grid-cols-2 gap-2 mb-2 @if($otroSi->isEmpty()) hidden @else grid @endif">
            <div class="rounded-xl border border-gray-200 shadow-md bg-white p-4">
                <h2 class="text-lg font-semibold mb-3">
                    Otrosís del Proyecto
                </h2>
                <table class="w-full text-sm text-center">
                    <thead class="bg-green-700 text-white text-xs uppercase">
                        <tr>
                            <th class="p-3">Concepto</th>
                            <th class="p-3">Valor</th>
                            <th class="p-3">Opciones</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($otroSi as $dato)
                            <tr class="border-t">
                                <td class="p-3">Otrosí N° {{ $dato->numero }}</td>
                                <td class="p-3">$ {{ number_format($dato->total_deve, 0, '.', ',') }}</td>
                                <td class="p-1">
                                    <div class=" flex items-center justify-center space-x-2">
                                        <a data-tooltip-target="tooltip-hover-contratoPdf-{{ $dato->id }}" data-tooltip-trigger="hover" 
                                                href="{{ route('otro_si.otroSiPdf', $dato->id) }}" 
                                                target="_blank" class="flex items-center justify-center w-10 h-10 text-white bg-slate-700 hover:bg-white hover:text-slate-800 border-2 border-slate-800 focus:ring-4
                                                focus:outline-none focus:ring-slate-300 font-medium rounded-full text-sm dark:bg-slate-600 dark:hover:bg-slate-700 dark:focus:ring-slate-800">
                                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7h1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h11.5M7 14h6m-6 3h6m0-10h.5m-.5 3h.5M7 7h3v3H7V7Z"></path>
                                            </svg>
                                        </a>
                                        <div id="tooltip-hover-contratoPdf-{{ $dato->id }}" role="tooltip" class="absolute z-10 inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs tooltip dark:bg-gray-700 opacity-0 invisible" style="position: absolute; inset: auto auto 0px 0px; margin: 0px; transform: translate3d(328px, 6.4px, 0px);" data-popper-placement="top">
                                            PDF Contrato
                                            <div class="tooltip-arrow" data-popper-arrow="" style="position: absolute; left: 0px; transform: translate3d(51.2px, 0px, 0px);"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>

    @include('planilla.modalPlanillaEntregable')

@endsection

@section('scripts')
    <script>
        const unidades = @json($unidades);
        const entregablesFromDB = @json($planillaEntregables);
    </script>
    <script src="{{ asset('js/planilla/create.js') }}?v={{ filemtime(public_path('js/planilla/create.js')) }}"></script>
@endsection
