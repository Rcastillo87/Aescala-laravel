@if ($cobros['activo'])
    @php
        $fmt = fn ($n) => '$ ' . number_format($n, 0, ',', '.');
        $badges = [
            'pendiente' => ['Pendiente',   'bg-gray-100 text-gray-600'],
            'cobrado'   => ['Por aprobar', 'bg-blue-100 text-blue-800'],
            'aprobado'  => ['Aprobado',    'bg-amber-100 text-amber-800'],
            'pagado'    => ['Pagado',      'bg-green-100 text-green-800'],
        ];
        $cols = 'xl:grid-cols-[minmax(0,1fr)_110px_140px_200px_140px_100px]';
        $labelClase = 'xl:hidden block text-xs font-medium text-gray-500 mb-1';
        $t = $cobros['totales'];
    @endphp

    <div id="seccion-cobros" class="mt-4 w-full rounded-2xl border-2 border-[#242e68] bg-white p-3 shadow-sm sm:p-4 relative">
        <div class="mb-3 flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
            <h2 class="text-lg font-bold text-[#242e68] sm:text-xl">Cobros del Proyecto</h2>
            <p class="text-xs text-gray-500">El valor a cobrar no puede superar el valor del ítem.</p>
        </div>

        <form id="formSaveCobros" action="{{ route('planilla.saveCobros') }}" method="POST" class="w-full">
            @csrf
            <input type="hidden" name="id_proyecto" value="{{ $proyecto->id }}">

            <div class="hidden gap-3 border-b border-gray-200 px-3 pb-2 text-xs font-semibold text-gray-500 xl:grid {{ $cols }}">
                <span>Concepto</span>
                <span class="text-right">Valor del ítem</span>
                <span>Valor a cobrar</span>
                <span>Aprobación</span>
                <span>Fecha de pago</span>
                <span class="text-center">Estado</span>
            </div>

            {{-- BLOQUE ENTREGABLES --}}
            @if(!empty($cobros['entregables']))
                @foreach ($cobros['entregables'] as $tipo => $items)
                    @php
                        $tipoNombre = \App\Models\PlanillaEntregables::$txTipo[$tipo] ?? 'Desconocido';
                        $puedeEditarGrupo = collect($items)->every('puedeEditar');
                    @endphp
                    
                    {{-- Header del Grupo con botones únicos --}}
                    <div class="mt-4 mb-2 flex items-center justify-between border-b pb-1">
                        <div class="text-sm font-bold text-[#242e68] uppercase tracking-wide">
                            ENTREGABLES - {{ $tipoNombre }}
                        </div>
                        @if($puedeEditarGrupo && auth()->user()->can('planilla.savePlantilla'))
                            <div class="flex items-center gap-2">
                                <button type="button" class="edit-entregable text-blue-600 hover:bg-blue-100 p-1 rounded text-xs font-medium flex items-center gap-1" data-tipo="{{ $tipo }}" title="Editar este grupo">
                                    ✏️ Editar
                                </button>
                                <button type="button" class="delete-entregable text-red-600 hover:bg-red-100 p-1 rounded text-xs font-medium flex items-center gap-1" data-tipo="{{ $tipo }}" title="Eliminar este grupo">
                                    🗑️ Eliminar
                                </button>
                            </div>
                        @endif
                    </div>

                    <ul class="grid grid-cols-1 gap-3 lg:grid-cols-2 xl:mt-0 xl:grid-cols-1 xl:gap-0">
                        @foreach ($items as $it)
                            @php
                                [$badgeTxt, $badgeClase] = $badges[$it['estado']];
                                if ($it['estado'] === 'aprobado') $badgeTxt .= ' ' . $it['aprobacion'] . '%';
                            @endphp
                            <li class="cobro-item relative grid grid-cols-2 items-start gap-3 rounded-xl border border-gray-200 bg-white p-3 {{ $cols }} xl:items-center xl:rounded-none xl:border-0 xl:border-b xl:px-3 xl:py-2" data-idx="{{ $it['idx'] }}" data-tipo="{{ $it['tipo'] }}">
                                
                                <div class="col-span-2 min-w-0 xl:col-span-1">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <p class="min-w-0 break-words text-sm font-semibold text-gray-800">{{ $it['concepto'] }}</p>
                                            
                                            {{-- INFORMACIÓN DETALLADA --}}
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ $it['cantidad'] }} {{ $unidades[$it['unidad']] ?? 'Unid' }} x {{ $fmt($it['valor_uni']) }} c/u
                                            </p>
                                            @if($it['porcentage'] > 0)
                                                <p class="text-xs text-blue-600 font-semibold mt-0.5">Porcentaje: {{ $it['porcentage'] }}%</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0 xl:hidden">
                                            <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $badgeClase }}">{{ $badgeTxt }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="min-w-0 xl:text-right">
                                    <span class="{{ $labelClase }}">Valor del ítem</span>
                                    <p class="whitespace-nowrap text-sm font-semibold text-gray-700">{{ $fmt($it['tope']) }}</p>
                                </div>

                                <div class="min-w-0">
                                    <label class="{{ $labelClase }}" for="cobro-valor-{{ $it['tipo'] }}-{{ $it['idx'] }}">Valor a cobrar</label>
                                    <select name="cobros[{{ $it['tipo'] }}][{{ $it['idx'] }}][valor_cobrar]" id="cobro-valor-{{ $it['tipo'] }}-{{ $it['idx'] }}" data-tope="{{ $it['tope'] }}" class="valor-select h-10 w-full rounded-lg border border-gray-300 px-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#242e68] disabled:bg-gray-100 disabled:text-gray-500 {{ $it['canValor'] ? '' : 'hidden' }}">
                                        <option value="0" @selected(($it['valor_cobrar'] == 0) || $it['valor_cobrar'] === null)>0</option>
                                        <option value="{{ $it['tope'] }}" @selected($it['valor_cobrar'] === $it['tope'])>{{ $fmt($it['tope']) }}</option>
                                        <option value="{{ $it['tope'] / 2 }}" @selected($it['valor_cobrar'] == $it['tope'] / 2)>{{ $fmt($it['tope'] / 2) }}</option>
                                    </select>
                                    <span id="cobro-valor-label-{{ $it['tipo'] }}-{{ $it['idx'] }}" class="valor-label h-10 flex items-center px-2 text-sm font-semibold text-gray-800 bg-gray-50 border border-gray-300 rounded-lg {{ $it['canValor'] ? 'hidden' : '' }}">
                                        {{ $it['valor_cobrar'] ? $fmt($it['valor_cobrar']) : '$ 0' }}
                                    </span>
                                    <input type="hidden" name="cobros[{{ $it['tipo'] }}][{{ $it['idx'] }}][valor_cobrar]" id="cobro-valor-hidden-{{ $it['tipo'] }}-{{ $it['idx'] }}" value="{{ $it['valor_cobrar'] ?? 0 }}" {{ $it['canValor'] ? 'disabled' : '' }}>
                                </div>

                                <div class="col-span-2 min-w-0 xl:col-span-1">
                                    <span class="{{ $labelClase }}">Aprobación</span>
                                    <div class="flex w-full overflow-hidden rounded-lg border border-gray-300" role="radiogroup">
                                        @foreach ([0 => 'Sin aprobar', 50 => '50%', 100 => '100%'] as $valorRadio => $txtRadio)
                                            <label class="aprobacion-label relative border-l border-gray-200 first:border-l-0 {{ $valorRadio === 0 ? 'flex-[1.5]' : 'flex-1' }} {{ $it['canAprob'] ? 'cursor-pointer' : 'cursor-not-allowed' }} {{ ($valorRadio === 50 && $it['esMitad']) ? 'hidden' : '' }}">
                                                <input type="radio" class="peer sr-only" name="cobros[{{ $it['tipo'] }}][{{ $it['idx'] }}][aprobacion]" value="{{ $valorRadio }}" @checked($it['aprobacion'] === $valorRadio) @disabled(!$it['canAprob'])>
                                                <span class="flex h-10 items-center justify-center whitespace-nowrap px-2 text-xs font-medium text-gray-600 peer-checked:bg-[#242e68] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-inset peer-focus-visible:ring-[#242e68] peer-disabled:opacity-60">{{ $txtRadio }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @if ($it['aprobacion'] > 0) <p class="mt-0.5 text-xs text-green-700">A pagar: {{ $fmt($it['valor_aprobado']) }}</p> @endif
                                </div>

                                <div class="min-w-0">
                                    <label class="{{ $labelClase }}" for="cobro-fecha-{{ $it['tipo'] }}-{{ $it['idx'] }}">Fecha de pago</label>
                                    <input type="date" id="cobro-fecha-{{ $it['tipo'] }}-{{ $it['idx'] }}" name="cobros[{{ $it['tipo'] }}][{{ $it['idx'] }}][fecha_pago]" value="{{ $it['fecha_pago'] }}" @disabled(!$it['canFecha']) class="h-10 w-full rounded-lg border border-gray-300 px-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#242e68] disabled:bg-gray-100 disabled:text-gray-500">
                                </div>
                                <div class="hidden justify-center xl:flex">
                                    <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $badgeClase }}">{{ $badgeTxt }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            @endif

            {{-- BLOQUE TIPO 5 --}}
            @if(!empty($cobros['tipo5']))
                <div class="mt-6 mb-2 text-sm font-bold text-[#242e68] uppercase tracking-wide border-b pb-1">
                    Porcentajes (Tipo 5)
                </div>
                <ul class="grid grid-cols-1 gap-3 lg:grid-cols-2 xl:mt-0 xl:grid-cols-1 xl:gap-0">
                    @foreach ($cobros['tipo5'] as $it)
                        @php
                            [$badgeTxt, $badgeClase] = $badges[$it['estado']];
                            if ($it['estado'] === 'aprobado') $badgeTxt .= ' ' . $it['aprobacion'] . '%';
                            $pctTxt = rtrim(rtrim(number_format($it['porcentage'], 2, '.', ''), '0'), '.');
                        @endphp
                        <li class="cobro-item grid grid-cols-2 items-start gap-3 rounded-xl border border-gray-200 bg-white p-3 {{ $cols }} xl:items-center xl:rounded-none xl:border-0 xl:border-b xl:px-3 xl:py-2" data-idx="{{ $it['idx'] }}" data-tipo="{{ $it['tipo'] }}">
                            <div class="col-span-2 min-w-0 xl:col-span-1">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="min-w-0 break-words text-sm font-semibold text-gray-800">{{ $it['concepto'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $it['en_pesos'] ? 'Valor fijo' : $pctTxt . '% del presupuesto' }}</p>
                                    </div>
                                    <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-medium xl:hidden {{ $badgeClase }}">{{ $badgeTxt }}</span>
                                </div>
                            </div>
                            <div class="min-w-0 xl:text-right"><span class="{{ $labelClase }}">Valor del ítem</span><p class="whitespace-nowrap text-sm font-semibold text-gray-700">{{ $fmt($it['tope']) }}</p></div>
                            
                            <div class="min-w-0">
                                <label class="{{ $labelClase }}" for="cobro-valor-{{ $it['tipo'] }}-{{ $it['idx'] }}">Valor a cobrar</label>
                                <select name="cobros[{{ $it['tipo'] }}][{{ $it['idx'] }}][valor_cobrar]" id="cobro-valor-{{ $it['tipo'] }}-{{ $it['idx'] }}" data-tope="{{ $it['tope'] }}" class="valor-select h-10 w-full rounded-lg border border-gray-300 px-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#242e68] disabled:bg-gray-100 disabled:text-gray-500 {{ $it['canValor'] ? '' : 'hidden' }}">
                                    <option value="0" @selected(($it['valor_cobrar'] == 0) || $it['valor_cobrar'] === null)>0</option>
                                    <option value="{{ $it['tope'] }}" @selected($it['valor_cobrar'] === $it['tope'])>{{ $fmt($it['tope']) }}</option>
                                    <option value="{{ $it['tope'] / 2 }}" @selected($it['valor_cobrar'] == $it['tope'] / 2)>{{ $fmt($it['tope'] / 2) }}</option>
                                </select>
                                <span id="cobro-valor-label-{{ $it['tipo'] }}-{{ $it['idx'] }}" class="valor-label h-10 flex items-center px-2 text-sm font-semibold text-gray-800 bg-gray-50 border border-gray-300 rounded-lg {{ $it['canValor'] ? 'hidden' : '' }}">{{ $it['valor_cobrar'] ? $fmt($it['valor_cobrar']) : '$ 0' }}</span>
                                <input type="hidden" name="cobros[{{ $it['tipo'] }}][{{ $it['idx'] }}][valor_cobrar]" id="cobro-valor-hidden-{{ $it['tipo'] }}-{{ $it['idx'] }}" value="{{ $it['valor_cobrar'] ?? 0 }}" {{ $it['canValor'] ? 'disabled' : '' }}>
                            </div>
                            <div class="col-span-2 min-w-0 xl:col-span-1">
                                <span class="{{ $labelClase }}">Aprobación</span>
                                <div class="flex w-full overflow-hidden rounded-lg border border-gray-300" role="radiogroup">
                                    @foreach ([0 => 'Sin aprobar', 50 => '50%', 100 => '100%'] as $valorRadio => $txtRadio)
                                        <label class="aprobacion-label relative border-l border-gray-200 first:border-l-0 {{ $valorRadio === 0 ? 'flex-[1.5]' : 'flex-1' }} {{ $it['canAprob'] ? 'cursor-pointer' : 'cursor-not-allowed' }} {{ ($valorRadio === 50 && $it['esMitad']) ? 'hidden' : '' }}">
                                            <input type="radio" class="peer sr-only" name="cobros[{{ $it['tipo'] }}][{{ $it['idx'] }}][aprobacion]" value="{{ $valorRadio }}" @checked($it['aprobacion'] === $valorRadio) @disabled(!$it['canAprob'])>
                                            <span class="flex h-10 items-center justify-center whitespace-nowrap px-2 text-xs font-medium text-gray-600 peer-checked:bg-[#242e68] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-inset peer-focus-visible:ring-[#242e68] peer-disabled:opacity-60">{{ $txtRadio }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @if ($it['aprobacion'] > 0) <p class="mt-0.5 text-xs text-green-700">A pagar: {{ $fmt($it['valor_aprobado']) }}</p> @endif
                            </div>
                            <div class="min-w-0">
                                <label class="{{ $labelClase }}" for="cobro-fecha-{{ $it['tipo'] }}-{{ $it['idx'] }}">Fecha de pago</label>
                                <input type="date" id="cobro-fecha-{{ $it['tipo'] }}-{{ $it['idx'] }}" name="cobros[{{ $it['tipo'] }}][{{ $it['idx'] }}][fecha_pago]" value="{{ $it['fecha_pago'] }}" @disabled(!$it['canFecha']) class="h-10 w-full rounded-lg border border-gray-300 px-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#242e68] disabled:bg-gray-100 disabled:text-gray-500">
                            </div>
                            <div class="hidden justify-center xl:flex"><span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $badgeClase }}">{{ $badgeTxt }}</span></div>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($cobros['puedeGuardar'])
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="w-full rounded-lg bg-blue-600 px-5 py-2.5 font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto">Guardar cobros</button>
                </div>
            @endif
        </form>

        {{-- CONSOLIDADO --}}
        @if ($t['n_aprobados'] > 0)
            <div class="mt-5 border-t border-gray-200 pt-4">
                <h3 class="mb-3 text-base font-bold text-[#242e68] sm:text-lg">Consolidado de pagos aprobados</h3>
                <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="min-w-0 rounded-xl border border-blue-200 bg-blue-50 p-3"><p class="text-xs font-medium text-blue-700">Total aprobado</p><p class="truncate text-lg font-bold text-blue-900">{{ $fmt($t['aprobado']) }}</p></div>
                    <div class="min-w-0 rounded-xl border border-green-200 bg-green-50 p-3"><p class="text-xs font-medium text-green-700">Pagado</p><p class="truncate text-lg font-bold text-green-900">{{ $fmt($t['pagado']) }}</p></div>
                    <div class="min-w-0 rounded-xl border border-amber-200 bg-amber-50 p-3"><p class="text-xs font-medium text-amber-700">Aprobado por pagar</p><p class="truncate text-lg font-bold text-amber-900">{{ $fmt($t['por_pagar']) }}</p></div>
                </div>
            </div>
        @endif
    </div>
@endif