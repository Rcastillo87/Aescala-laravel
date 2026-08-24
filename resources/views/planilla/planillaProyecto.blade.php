@extends('layouts.app')
@section('content')
    <div class="mx-2">

        {{-- ================================================== --}}
        {{-- CABECERA DEL PROYECTO --}}
        {{-- ================================================== --}}
        <div class="flex-1 min-w-0">
            <div class="py-1 mb-1 border-b border-gray-100 flex items-center justify-between">
                <div class="">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Proyecto</span>
                    <h3 class="text-base sm:text-lg font-bold text-gray-800 leading-tight">
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

            @php
                // Se arma dinámicamente la lista de datos a mostrar para no repetir
                // el mismo bloque de marcado 8 veces (antes eran divs idénticos copiados y pegados).
                $infoItems = [
                    [
                        'label' => 'Ubicación',
                        'value' => $departamentos[intval($proyecto['departamento'])]['departamento'] . ' – ' .
                                   $departamentos[intval($proyecto['departamento'])]['ciudades'][$proyecto['ciudad']] . ' – ' .
                                   ($proyecto->ubicacion !== null ? ($ubicacion[$proyecto->ubicacion] ?? 'N/A') : 'N/A'),
                    ],
                    ['label' => 'Dirección', 'value' => $proyecto->direccion, 'truncate' => true],
                    ['label' => 'Contacto Cliente', 'value' => $proyecto->nombre_cliente],
                    ['label' => 'Teléfono', 'value' => $proyecto->telefono_cliente, 'mono' => true],
                ];

                if ($proyecto->user) {
                    $infoItems[] = ['label' => 'Residente', 'value' => $proyecto->user?->nombre_completo];
                }
                if ($proyecto->userOB) {
                    $infoItems[] = ['label' => 'Cont. Obra Blanca', 'value' => $proyecto->userOB['nombre_completo']];
                }
                if ($proyecto->user_carpinteria) {
                    $infoItems[] = ['label' => 'Cont. Carpintería', 'value' => $proyecto->user_carpinteria];
                }
                if ($proyecto->userDiseno) {
                    $infoItems[] = ['label' => 'Diseñador', 'value' => $proyecto->userDiseno['nombre_completo']];
                }
                if ($proyecto->fec_fin_real) {
                    $infoItems[] = ['label' => 'Fecha de Entrega', 'value' => explode(' ', $proyecto->fec_fin_real)[0], 'bold' => true];
                }
                if ($proyecto->paz_salvo == 1) {
                    $infoItems[] = ['label' => 'Paz & salvo', 'value' => $proyecto->apaz, 'raw' => true, 'bold' => true];
                }
                $infoItems[] = ['label' => 'Area Privada', 'value' => $proyecto->area_privada . ' mt²', 'bold' => true];
            @endphp

            {{-- Grid de datos --}}
            <div class="grid grid-cols-1 xs:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-x-4 gap-y-3">
                @foreach ($infoItems as $item)
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">
                            {{ $item['label'] }}
                        </p>
                        <p class="text-sm text-gray-700 leading-snug
                            {{ ($item['truncate'] ?? false) ? 'truncate' : '' }}
                            {{ ($item['bold'] ?? false) ? 'font-medium' : '' }}
                            {{ ($item['mono'] ?? false) ? 'font-mono' : '' }}">
                            @if($item['raw'] ?? false)
                                {!! $item['value'] !!}
                            @else
                                {{ $item['value'] }}
                            @endif
                        </p>
                    </div>
                @endforeach

                @if($proyecto->observacion)
                    <div class="min-w-0 xs:col-span-2 md:col-span-3 xl:col-span-4">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Observación</p>
                        <p class="text-sm text-gray-600 leading-snug italic">{{ $proyecto->observacion }}</p>
                    </div>
                @endif
            </div>
        </div>

        <hr class="my-4 border-gray-200">

        {{-- ================================================== --}}
        {{-- CONFIGURACIÓN DEL PROYECTO --}}
        {{-- ================================================== --}}
        <div class="bg-white border-2 border-[#242e68] rounded-2xl p-4 shadow-sm w-full">
            <div class="flex items-center gap-2 mb-4 w-full">
                <h2 class="text-xl font-bold text-[#242e68]">
                    Configuración del Proyecto
                </h2>
                <button type="button"
                    data-tooltip-target="tooltip-config-general"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zM9 8a1 1 0 112 0v5a1 1 0 11-2 0V8zm1-4a1 1 0 100 2 1 1 0 000-2z"
                            clip-rule="evenodd"/>
                    </svg>
                </button>
                <div id="tooltip-config-general"
                    role="tooltip"
                    class="absolute z-10 invisible inline-block w-72 px-3 py-2 text-sm text-white bg-gray-900 rounded-lg shadow-xs opacity-0 tooltip">
                    Estos valores se usan para calcular automáticamente el costo del proyecto según su área.
                    Se sugieren según el área registrada, pero puedes aceptarlos o eliminarlos cuando quieras.
                </div>
            </div>

            {{-- ---------- FILA PRINCIPAL: Porcentajes (Tipo 3) + Presupuesto Por Proyecto (Tipo 1) a la izquierda, Costos adicionales a la derecha ---------- --}}
            @php
            // Tipos que solo requieren un valor simple. 'suggested' es el valor de partida
            // que se muestra en el input cuando aún no hay configuración guardada.
            $compactConfigs = [
                2 => ['label' => 'Presupuesto Enchape Por Area', 'suggested' => optional($dataValorAreaEnchape)->valor_intervalo ?? 0],
                4 => ['label' => 'Obra blanca', 'suggested' => 0],
                5 => ['label' => 'Carpintería', 'suggested' => 0],
                6 => ['label' => 'Excedente Enchap', 'suggested' => 0],
            ];
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 w-full mb-4">
                <div>
                {{-- Tipo 1: Presupuesto Por Proyecto --}}
                <div class="config-block bg-white border rounded-2xl shadow-md p-5 w-full"
                    data-tipo="1"
                    data-id-proyecto="{{ $proyecto->id }}"
                    data-kind="simple">

                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-md font-semibold text-[#242e68]">
                            Presupuesto Por Proyecto
                        </h3>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            Tipo 1
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mb-3">
                        Costo por m² calculado según el área privada del proyecto
                        ({{ $areaProyecto ?? $proyecto->area_privada }} m²).
                    </p>

                    <div class="state-saved {{ $configProyecto->has(1) ? '' : 'hidden' }}">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex items-center justify-between">
                            <div>
                                <span class="text-xs text-green-700 font-medium block">
                                    Configuración aceptada
                                </span>
                                <span class="saved-value text-lg font-bold text-green-800">
                                    {{ $configProyecto->has(1) ? '$ ' . number_format($configProyecto[1]->valor, 0, ',', '.') : '' }}
                                </span>
                            </div>

                            <div class="space-x-2">
                                @if($configProyecto->has(1) && $configProyecto->has(3))
                                    <a href="{{ route('planilla.pdfConfigPlanilla', [1, $proyecto->id]) }}"
                                        target="_blank"
                                        id="btn-pdf-tipo-1"
                                        class="btn-pdf-config px-3 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors"
                                        title="Ver PDF">
                                        📄 PDF
                                    </a>
                                @endif

                                <button type="button"
                                    class="btn-delete-config text-red-600 hover:bg-red-100 p-1 rounded-lg"
                                    data-tipo="1">
                                    🗑️
                                </button>
                            </div>
                        </div>

                        @if($configProyecto->has(1) && $configProyecto->has(3))
                            @php
                                $totalDesglose1 = 0;
                                $totalPorcentaje1 = 0;
                            @endphp
                            <div class="mt-2 bg-green-50/60 border border-green-200 rounded-lg p-3">
                                <span class="text-xs text-green-700 font-medium block mb-2">Desglose por porcentajes</span>
                                <div class="grid grid-cols-[minmax(0,1fr)_55px_95px] sm:grid-cols-[minmax(0,1fr)_70px_110px] md:grid-cols-[minmax(0,1fr)_80px_120px] gap-2 text-xs font-semibold text-gray-600 border-b border-green-200 pb-2 mb-1">
                                    <span>Concepto</span>
                                    <span class="text-right">%</span>
                                    <span class="text-right">Valor</span>
                                </div>
                                <ul class="space-y-1 mb-2">
                                    @foreach($configProyecto[3]->valor as $item)
                                        @php
                                            $porcentajeItem = (float) $item['porcentage'];
                                            $montoItem = $configProyecto[1]->valor * ($porcentajeItem / 100);

                                            // Usamos ?? 0 para evitar el error si la clave no existe
                                            $enPesos = $item['en_pesos'] ?? 0;

                                            $totalDesglose1 += ($enPesos == 1) ? $porcentajeItem : $montoItem;
                                            $totalPorcentaje1 += ($enPesos == 1) ? 0 : $porcentajeItem;
                                        @endphp

                                        <li class="grid grid-cols-[minmax(0,1fr)_55px_95px] sm:grid-cols-[minmax(0,1fr)_70px_110px] md:grid-cols-[minmax(0,1fr)_80px_120px] gap-2 text-sm items-start">
                                            <span class="text-gray-700 min-w-0 break-words">
                                                {{ $item['concepto'] }}
                                            </span>
                                            <span class="text-right text-gray-700 whitespace-nowrap">
                                                {{ ($enPesos == 0) ? $porcentajeItem . '%' : '-' }}
                                            </span>
                                            <span class="text-right font-semibold text-green-800 whitespace-nowrap">
                                                $ {{ number_format((($enPesos == 0) ? $montoItem : $porcentajeItem), 0, ',', '.') }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="grid grid-cols-[minmax(0,1fr)_55px_95px] sm:grid-cols-[minmax(0,1fr)_70px_110px] md:grid-cols-[minmax(0,1fr)_80px_120px] gap-2 text-sm font-bold border-t border-green-200 pt-2">
                                    <span class="text-gray-800">Total</span>
                                    <span class="text-right text-gray-800 whitespace-nowrap">{{ $totalPorcentaje1 }}%</span>
                                    <span class="text-right text-green-800 whitespace-nowrap">
                                        $ {{ number_format($totalDesglose1, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="state-suggested {{ $configProyecto->has(1) ? 'hidden' : '' }}">
                        @if($dataValorArea)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <span class="text-xs text-yellow-700 font-medium block mb-1">
                                    Valor sugerido
                                </span>
                                <div class="flex items-center gap-1 mb-2">
                                    <span class="text-yellow-800 font-semibold">$</span>
                                    <input type="number"
                                        min="0"
                                        step="1"
                                        class="moneda-cop suggested-value-input w-full border border-yellow-300 rounded-lg px-2 py-1 text-lg font-bold text-yellow-800 bg-white focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                        value="{{ $dataValorArea->valor_intervalo }}">
                                </div>
                                <p class="text-xs text-gray-500 mb-2">
                                    Rango: {{ $dataValorArea->area_min }} m² -
                                    {{ $dataValorArea->area_max }} m²
                                    (año {{ $dataValorArea->año }})
                                </p>

                                <button type="button"
                                    class="btn-accept-config w-full bg-[#242e68] hover:bg-[#1a2150] text-white text-sm font-medium rounded-lg px-3 py-2"
                                    data-tipo="1">
                                    Aceptar configuración
                                </button>
                            </div>
                        @else
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-center">
                                <span class="text-sm text-gray-500">
                                    No hay valor configurado para esta área.
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
                </div>

                <div class="space-y-4">
                {{-- Tipo 3: Porcentajes del Proyecto Planilla Base --}}
                <div class="config-block bg-white border rounded-2xl shadow-md p-5 w-full"
                    data-tipo="3"
                    data-id-proyecto="{{ $proyecto->id }}"
                    data-kind="multi">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-md font-semibold text-[#242e68]">
                            Porcentajes del Proyecto Planilla Base
                        </h3>
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            Tipo 3
                        </span>
                    </div>

                    <p class="text-xs text-gray-500 mb-3">
                        Porcentajes aplicados sobre el proyecto
                        (administración, imprevistos, utilidad, etc.).
                    </p>

                    <div class="state-saved {{ $configProyecto->has(3) ? '' : 'hidden' }}">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <span class="text-xs text-green-700 font-medium block mb-2">
                                Configuración aceptada
                            </span>
                            <ul class="saved-list space-y-1 mb-2 {{ ($configProyecto->has(3) && $configProyecto->has(1)) ? 'hidden' : '' }}">
                                @if($configProyecto->has(3))
                                    @foreach($configProyecto[3]->valor as $item)
                                        <li class="flex justify-between text-sm">
                                            <span class="text-gray-700">
                                                {{ $item['concepto'] }}
                                            </span>
                                            <span class="font-semibold text-green-800">
                                                {{ $item['porcentage'] }}
                                                @if ( isset($item['en_pesos']) && ($item['en_pesos'] == 1))
                                                    $
                                                @else
                                                    %
                                                @endif
                                            </span>
                                        </li>
                                    @endforeach

                                    <div class="flex justify-between text-sm font-bold border-t border-green-200 pt-2 mb-2">
                                        <span class="text-gray-800">Total</span>
                                        <span class="text-green-800">
                                            {{ rtrim(
                                                rtrim(
                                                    number_format(
                                                        collect($configProyecto[3]->valor)
                                                            ->where('en_pesos', 0)
                                                            ->sum('porcentage'),
                                                        2,
                                                        '.',
                                                        ''
                                                    ),
                                                    '0'
                                                ),
                                                '.'
                                            ) }}%
                                        </span>
                                    </div>
                                @endif
                            </ul>

                            <button type="button"
                                class="btn-delete-config w-full text-red-600 hover:bg-red-100 text-sm font-medium rounded-lg px-3 py-2 border border-red-200"
                                data-tipo="3">
                                🗑️ Eliminar configuración
                            </button>
                        </div>
                    </div>

                    <div class="state-suggested {{ $configProyecto->has(3) ? 'hidden' : '' }}">
                        @if($dataConfigPorcentajes && $dataConfigPorcentajes->count())
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <span class="text-xs text-yellow-700 font-medium block mb-2">
                                    Porcentajes sugeridos
                                    ({{ $dataConfigPorcentajes->first()->año }})
                                </span>
                                <ul class="suggested-list space-y-1 mb-3">
                                    @foreach($dataConfigPorcentajes as $c)
                                        <li class="suggested-item flex items-center justify-between gap-2 text-sm"
                                            data-concepto="{{ $c->concepto }}">
                                            <span class="text-gray-700 flex-1">
                                                {{ $c->concepto }}
                                            </span>
                                            <div class="flex items-center gap-1">
                                                <input type="number"
                                                    min="0"
                                                    step="0.01"
                                                    class="suggested-porcentage-input w-16 text-right border border-yellow-300 rounded px-1 py-0.5 font-semibold text-yellow-800 bg-white focus:outline-none focus:ring-2 focus:ring-yellow-400"
                                                    value="{{ $c->porcentage }}">
                                                @if ($c->en_pesos == 0)
                                                    <span class="text-gray-500">%</span>
                                                @else
                                                    <span class="text-gray-500">$</span>
                                                @endif
                                            </div>
                                            <input type="hidden"
                                                class="suggested-en-pesos"
                                                value="{{ $c->en_pesos }}">
                                            <button type="button"
                                                class="remove-suggested-item text-red-500 hover:text-red-700 px-1"
                                                title="Eliminar">
                                                ✕
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="flex justify-between text-sm font-bold border-t border-yellow-300 pt-2 mb-3">
                                    <span class="text-gray-800">Total</span>
                                    <span class="suggested-total text-yellow-800">
                                        {{ $dataConfigPorcentajes->where('en_pesos', 0)->sum('porcentage') }}%
                                    </span>
                                </div>

                                <button type="button"
                                    class="btn-accept-config w-full bg-[#242e68] hover:bg-[#1a2150] text-white text-sm font-medium rounded-lg px-3 py-2"
                                    data-tipo="3">
                                    Aceptar configuración
                                </button>
                            </div>
                        @else
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-center">
                                <span class="text-sm text-gray-500">
                                    No hay porcentajes configurados.
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
                    <h4 class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-3">
                        Costos adicionales
                    </h4>

                    <div class="space-y-3">
                    @foreach ($compactConfigs as $tipo => $cfg)
                        <div class="config-block bg-white border border-gray-200 rounded-xl px-3 py-2 shadow-sm"
                            data-tipo="{{ $tipo }}"
                            data-kind="simple">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-[#242e68] text-xs">
                                    {{ $cfg['label'] }}:
                                </span>
                                <span class="bg-blue-50 text-blue-700 text-[9px] font-medium px-2 py-0.2 rounded-full">
                                    Tipo {{ $tipo }}
                                </span>
                            </div>

                            {{-- ESTADO: GUARDADO --}}
                            <div class="state-saved {{ isset($configProyecto[$tipo]) ? '' : 'hidden' }}">
                                <div class="bg-green-50 border border-green-200 rounded-lg px-2.5 py-1.5 flex items-center justify-between">
                                    <span class="saved-value text-xs font-bold text-green-800">
                                        {{ isset($configProyecto[$tipo]) ? '$ ' . number_format($configProyecto[$tipo]->valor, 0, ',', '.') : '' }}
                                    </span>
                                    <button type="button"
                                        class="btn-delete-config text-red-600 hover:bg-red-100 p-1 rounded text-xs"
                                        data-tipo="{{ $tipo }}"
                                        title="Eliminar">
                                        🗑️
                                    </button>
                                </div>
                            </div>

                            {{-- ESTADO: EDITABLE / SUGERIDO --}}
                            <div class="state-suggested {{ isset($configProyecto[$tipo]) ? 'hidden' : '' }}">
                                <div class="flex gap-1.5">
                                    <input type="number"
                                        step="any"
                                        class="moneda-cop suggested-value-input w-full border border-gray-300 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#242e68]"
                                        value="{{ $cfg['suggested'] }}"
                                        placeholder="Editable">
                                    <button type="button"
                                        class="btn-accept-config bg-[#242e68] hover:bg-[#1a2150] text-white px-2.5 py-1 rounded-lg text-[11px] font-medium shrink-0"
                                        data-tipo="{{ $tipo }}"
                                        title="Guardar">
                                        💾 Guardar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="flex items-center justify-between px-3 py-2 bg-gray-50/80 rounded-lg border border-gray-100">
                        <span class="font-bold text-[#242e68] text-xs">P. to Carpin:</span>
                        <span class="font-semibold text-gray-700 text-xs">$ 0</span>
                    </div>

                    <div class="flex items-center justify-between px-3 py-2 bg-gray-50/80 rounded-lg border border-gray-100">
                        <span class="font-bold text-[#242e68] text-xs">Adicionales:</span>
                        <span class="font-semibold text-gray-700 text-xs">
                            $ {{ number_format($valOtrosis, 0, ',', '.') }}
                        </span>
                    </div>
                    </div>
                </div>
            </div>

            <form id="formAcceptConfig"
                action="{{ route('planilla.saveConfigPlantilla') }}"
                method="POST"
                class="hidden">
                @csrf
                <input type="hidden" name="id_proyecto" value="{{ $proyecto->id }}">
                <input type="hidden" name="tipo" id="accept-tipo">
                <input type="hidden" name="valor_config" id="accept-valor">
                <input type="hidden" name="conceptos" id="accept-conceptos">
                <button type="submit"></button>
            </form>

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

        {{-- ================================================== --}}
        {{-- ENTREGABLES --}}
        {{-- ================================================== --}}
        <div class="flex justify-start w-full">
            <x-secondary-button class="flex items-center gap-2 px-4 py-2 cursor-pointer" id="btn-open-modal"
                x-on:click="$dispatch('open-modal', 'modalPlanillaEntregable-modal')"
                x-data="">
                <b>+</b>
                <span>Agregar Entregable</span>
            </x-secondary-button>
        </div>

        <div class="py-2 justify-start w-full space-y-2 @if(empty($planillaEntregables)) hidden @endif">
            <h2 class="text-xl font-bold text-[#242e68]">Entregables Agregados</h2>

            <form id="savePlantilla" action="{{ route('planilla.savePlantilla') }}" method="POST" class="w-full space-y-4">
                <input type="hidden" name="id_proyecto" value="{{ $proyecto->id }}">

                {{-- Aquí solo se renderizan las tarjetas dinámicamente --}}
                <div id="area-div" class="mt-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 w-full [&>div]:w-full"></div>

                {{-- El botón de guardar se queda fijo aquí, fuera de area-div, para que no lo borre el JS --}}
                <div id="entregables-container" class="hidden w-full flex justify-end mt-4">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-medium">
                        Guardar Entregables
                    </button>
                </div>
            </form>
        </div>

        <hr class="my-4 border-gray-200">

        {{-- ================================================== --}}
        {{-- OTROSÍS --}}
        {{-- ================================================== --}}
        <div class="lg:grid-cols-2 gap-2 mb-2 @if($otroSi->isEmpty()) hidden @else grid @endif">
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
                                    <div class="flex items-center justify-center space-x-2">
                                        <a data-tooltip-target="tooltip-hover-contratoPdf-{{ $dato->id }}"
                                            data-tooltip-trigger="hover"
                                            href="{{ route('otro_si.otroSiPdf', $dato->id) }}"
                                            target="_blank"
                                            class="flex items-center justify-center w-10 h-10 text-white bg-slate-700 hover:bg-white hover:text-slate-800 border-2 border-slate-800 focus:ring-4
                                            focus:outline-none focus:ring-slate-300 font-medium rounded-full text-sm dark:bg-slate-600 dark:hover:bg-slate-700 dark:focus:ring-slate-800">
                                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7h1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h11.5M7 14h6m-6 3h6m0-10h.5m-.5 3h.5M7 7h3v3H7V7Z"></path>
                                            </svg>
                                        </a>
                                        {{-- Nota: se quitó el `style="transform: translate3d(...)"` y `data-popper-placement`
                                             que estaban hardcodeados en el original — esos valores los calcula
                                             Flowbite/Popper.js en tiempo real; dejarlos fijos en el Blade rompe
                                             el posicionamiento del tooltip en pantallas distintas. --}}
                                        <div id="tooltip-hover-contratoPdf-{{ $dato->id }}"
                                            role="tooltip"
                                            class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs tooltip dark:bg-gray-700 opacity-0">
                                            PDF Contrato
                                            <div class="tooltip-arrow" data-popper-arrow=""></div>
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