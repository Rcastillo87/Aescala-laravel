{{-- ================================================== --}}
{{-- COBROS DEL PROYECTO (Tipo 3) + CONSOLIDADO         --}}
{{-- Se muestra cuando Tipo 1 y Tipo 3 están aceptados. --}}
{{-- Recibe: $cobros, $proyecto                         --}}
{{-- ================================================== --}}
@if ($cobros['activo'])
    @php
        $fmt = fn ($n) => '$ ' . number_format($n, 0, ',', '.');

        $badges = [
            'pendiente' => ['Pendiente',   'bg-gray-100 text-gray-600'],
            'cobrado'   => ['Por aprobar', 'bg-blue-100 text-blue-800'],
            'aprobado'  => ['Aprobado',    'bg-amber-100 text-amber-800'],
            'pagado'    => ['Pagado',      'bg-green-100 text-green-800'],
        ];

        // Columnas de la fila en pantallas xl: concepto | valor ítem | valor a cobrar | aprobación | fecha | estado
        $cols = 'xl:grid-cols-[minmax(0,1fr)_110px_140px_200px_140px_100px]';

        $labelClase = 'xl:hidden block text-xs font-medium text-gray-500 mb-1';
        $t = $cobros['totales'];
    @endphp

    <div id="seccion-cobros" class="mt-4 w-full rounded-2xl border-2 border-[#242e68] bg-white p-3 shadow-sm sm:p-4">

        <div class="mb-3 flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
            <h2 class="text-lg font-bold text-[#242e68] sm:text-xl">Cobros del Proyecto</h2>
            <p class="text-xs text-gray-500">
                El valor a cobrar no puede superar el valor del ítem.
            </p>
        </div>

        <form id="formSaveCobros"
            action="{{ route('planilla.saveCobros') }}"
            method="POST"
            class="w-full">
            @csrf
            <input type="hidden" name="id_proyecto" value="{{ $proyecto->id }}">

            {{-- Encabezado de columnas (solo xl; en pantallas menores cada campo lleva su etiqueta) --}}
            <div class="hidden gap-3 border-b border-gray-200 px-3 pb-2 text-xs font-semibold text-gray-500 xl:grid {{ $cols }}">
                <span>Concepto</span>
                <span class="text-right">Valor del ítem</span>
                <span>Valor a cobrar</span>
                <span>Aprobación</span>
                <span>Fecha de pago</span>
                <span class="text-center">Estado</span>
            </div>

            <ul class="mt-3 grid grid-cols-1 gap-3 lg:grid-cols-2 xl:mt-0 xl:grid-cols-1 xl:gap-0">
                @foreach ($cobros['items'] as $it)
                    @php
                        [$badgeTxt, $badgeClase] = $badges[$it['estado']];
                        if ($it['estado'] === 'aprobado') {
                            $badgeTxt .= ' ' . $it['aprobacion'] . '%';
                        }
                        $pctTxt = rtrim(rtrim(number_format($it['porcentage'], 2, '.', ''), '0'), '.');
                    @endphp

                    <li class="cobro-item grid grid-cols-2 items-start gap-3 rounded-xl border border-gray-200 bg-white p-3 {{ $cols }} xl:items-center xl:rounded-none xl:border-0 xl:border-b xl:px-3 xl:py-2"
                        data-idx="{{ $it['idx'] }}">

                        {{-- Concepto (+ estado en pantallas menores a xl) --}}
                        <div class="col-span-2 min-w-0 xl:col-span-1">
                            <div class="flex items-start justify-between gap-2">
                                <p class="min-w-0 break-words text-sm font-semibold text-gray-800">{{ $it['concepto'] }}</p>
                                <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-medium xl:hidden {{ $badgeClase }}">
                                    {{ $badgeTxt }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500">
                                {{ $it['en_pesos'] ? 'Valor fijo' : $pctTxt . '% del presupuesto' }}
                            </p>
                        </div>

                        {{-- Valor del ítem (tope) --}}
                        <div class="min-w-0 xl:text-right">
                            <span class="{{ $labelClase }}">Valor del ítem</span>
                            <p class="whitespace-nowrap text-sm font-semibold text-gray-700">{{ $fmt($it['tope']) }}</p>
                        </div>

                        {{-- Valor a cobrar --}}
                        <div class="min-w-0">
                            <label class="{{ $labelClase }}" for="cobro-valor-{{ $it['idx'] }}">Valor a cobrar</label>
                            <input type="number"
                                id="cobro-valor-{{ $it['idx'] }}"
                                name="cobros[{{ $it['idx'] }}][valor_cobrar]"
                                value="{{ $it['valor_cobrar'] }}"
                                min="0"
                                max="{{ $it['tope'] }}"
                                step="1"
                                inputmode="numeric"
                                data-tope="{{ $it['tope'] }}"
                                data-concepto="{{ $it['concepto'] }}"
                                placeholder="0"
                                @disabled(!$it['canValor'])
                                class="h-10 w-full rounded-lg border border-gray-300 px-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#242e68] disabled:bg-gray-100 disabled:text-gray-500">
                            <p class="aviso-tope mt-0.5 hidden text-xs text-red-600">
                                No puede superar {{ $fmt($it['tope']) }}
                            </p>
                        </div>

                        {{-- Aprobación --}}
                        <div class="col-span-2 min-w-0 xl:col-span-1">
                            <span class="{{ $labelClase }}">Aprobación</span>
                            <div class="flex w-full overflow-hidden rounded-lg border border-gray-300" role="radiogroup" aria-label="Aprobación de {{ $it['concepto'] }}">
                                @foreach ([0 => 'Sin aprobar', 50 => '50%', 100 => '100%'] as $valorRadio => $txtRadio)
                                    <label class="relative border-l border-gray-200 first:border-l-0 {{ $valorRadio === 0 ? 'flex-[1.5]' : 'flex-1' }} {{ $it['canAprob'] ? 'cursor-pointer' : 'cursor-not-allowed' }}">
                                        <input type="radio"
                                            class="peer sr-only"
                                            name="cobros[{{ $it['idx'] }}][aprobacion]"
                                            value="{{ $valorRadio }}"
                                            @checked($it['aprobacion'] === $valorRadio)
                                            @disabled(!$it['canAprob'])>
                                        <span class="flex h-10 items-center justify-center whitespace-nowrap px-2 text-xs font-medium text-gray-600 peer-checked:bg-[#242e68] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-inset peer-focus-visible:ring-[#242e68] peer-disabled:opacity-60">
                                            {{ $txtRadio }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @if ($it['aprobacion'] > 0)
                                <p class="mt-0.5 text-xs text-green-700">A pagar: {{ $fmt($it['valor_aprobado']) }}</p>
                            @endif
                        </div>

                        {{-- Fecha de pago --}}
                        <div class="min-w-0">
                            <label class="{{ $labelClase }}" for="cobro-fecha-{{ $it['idx'] }}">Fecha de pago</label>
                            <input type="date"
                                id="cobro-fecha-{{ $it['idx'] }}"
                                name="cobros[{{ $it['idx'] }}][fecha_pago]"
                                value="{{ $it['fecha_pago'] }}"
                                @disabled(!$it['canFecha'])
                                class="h-10 w-full rounded-lg border border-gray-300 px-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#242e68] disabled:bg-gray-100 disabled:text-gray-500">
                        </div>

                        {{-- Estado (solo xl) --}}
                        <div class="hidden justify-center xl:flex">
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $badgeClase }}">{{ $badgeTxt }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>

            @if ($cobros['puedeGuardar'])
                <div class="mt-4 flex justify-end">
                    <button type="submit"
                        class="w-full rounded-lg bg-blue-600 px-5 py-2.5 font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto">
                        Guardar cobros
                    </button>
                </div>
            @endif
        </form>

        {{-- ================================================== --}}
        {{-- CONSOLIDADO DE PAGOS APROBADOS                     --}}
        {{-- ================================================== --}}
        @if ($t['n_aprobados'] > 0)
            <div class="mt-5 border-t border-gray-200 pt-4">
                <h3 class="mb-3 text-base font-bold text-[#242e68] sm:text-lg">Consolidado de pagos aprobados</h3>

                <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="min-w-0 rounded-xl border border-blue-200 bg-blue-50 p-3">
                        <p class="text-xs font-medium text-blue-700">Total aprobado</p>
                        <p class="truncate text-lg font-bold text-blue-900">{{ $fmt($t['aprobado']) }}</p>
                    </div>
                    <div class="min-w-0 rounded-xl border border-green-200 bg-green-50 p-3">
                        <p class="text-xs font-medium text-green-700">Pagado</p>
                        <p class="truncate text-lg font-bold text-green-900">{{ $fmt($t['pagado']) }}</p>
                    </div>
                    <div class="min-w-0 rounded-xl border border-amber-200 bg-amber-50 p-3">
                        <p class="text-xs font-medium text-amber-700">Aprobado por pagar</p>
                        <p class="truncate text-lg font-bold text-amber-900">{{ $fmt($t['por_pagar']) }}</p>
                    </div>
                </div>

                {{-- Tabla desde md --}}
                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-600">
                            <tr>
                                <th class="p-2 text-left font-semibold">Concepto</th>
                                <th class="p-2 text-right font-semibold">Valor a cobrar</th>
                                <th class="p-2 text-center font-semibold">Aprobado</th>
                                <th class="p-2 text-right font-semibold">Valor aprobado</th>
                                <th class="p-2 text-center font-semibold">Fecha de pago</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cobros['items'] as $it)
                                @if ($it['aprobacion'] > 0)
                                    <tr class="border-t border-gray-100">
                                        <td class="min-w-0 break-words p-2 text-gray-800">{{ $it['concepto'] }}</td>
                                        <td class="whitespace-nowrap p-2 text-right text-gray-700">{{ $fmt($it['valor_cobrar']) }}</td>
                                        <td class="whitespace-nowrap p-2 text-center text-gray-700">{{ $it['aprobacion'] }}%</td>
                                        <td class="whitespace-nowrap p-2 text-right font-semibold text-gray-900">{{ $fmt($it['valor_aprobado']) }}</td>
                                        <td class="whitespace-nowrap p-2 text-center {{ $it['fecha_pago'] ? 'text-green-700' : 'text-amber-700' }}">
                                            {{ $it['fecha_pago'] ?? 'Pendiente de pago' }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-[#242e68] font-bold text-[#242e68]">
                                <td class="p-2" colspan="3">Total aprobado</td>
                                <td class="whitespace-nowrap p-2 text-right">{{ $fmt($t['aprobado']) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Tarjetas debajo de md --}}
                <ul class="space-y-2 md:hidden">
                    @foreach ($cobros['items'] as $it)
                        @if ($it['aprobacion'] > 0)
                            <li class="rounded-xl border border-gray-200 p-3 text-sm">
                                <p class="break-words font-semibold text-gray-800">{{ $it['concepto'] }}</p>
                                <div class="mt-1 flex items-baseline justify-between gap-2">
                                    <span class="text-xs text-gray-500">{{ $fmt($it['valor_cobrar']) }} × {{ $it['aprobacion'] }}%</span>
                                    <span class="whitespace-nowrap font-bold text-gray-900">{{ $fmt($it['valor_aprobado']) }}</span>
                                </div>
                                <p class="mt-1 text-xs {{ $it['fecha_pago'] ? 'text-green-700' : 'text-amber-700' }}">
                                    {{ $it['fecha_pago'] ? 'Pagado el ' . $it['fecha_pago'] : 'Pendiente de pago' }}
                                </p>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif