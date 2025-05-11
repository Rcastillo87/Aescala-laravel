<!-- Modal -->
<x-modal name="compartivo-modal" maxWidth="6xl">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-4">Comparativo Despacho vs Cotizacion</h2>

        <div id="listaComparativoEmpy"  class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2 hidden">
            <div class="text-center py-8 col-span-1 md:col-span-2">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">No hay data para mostrar</h3>
                <p class="mt-1 text-gray-500">Realice despachos y cotizaciones para poder habilitar este modulo.</p>
            </div>
        </div>

        <div id="listaComparativo">
            <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-styled-tab" 
                    data-tabs-toggle="#default-styled-tab-content" data-tabs-active-classes="text-purple-600 hover:text-purple-600 dark:text-purple-500 dark:hover:text-purple-500 border-purple-600 dark:border-purple-500" 
                    data-tabs-inactive-classes="dark:border-transparent text-gray-500 hover:text-gray-600 dark:text-gray-400 border-gray-100 hover:border-gray-300 dark:border-gray-700 dark:hover:text-gray-300" role="tablist">
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg" id="listCompartivo-styled-tab" data-tabs-target="#styled-listCompartivo" type="button" role="tab" 
                        aria-controls="listCompartivo" aria-selected="false">Lista Comparativo</button>
                    </li>
                    <li role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="grafica-styled-tab" 
                        data-tabs-target="#styled-grafica" type="button" role="tab" aria-controls="grafica" aria-selected="false">Grafica</button>
                    </li>
                </ul>
            </div>
            <div id="default-styled-tab-content">

                <div class="hidden p-4 rounded-lg bg-white dark:bg-gray-800" id="styled-listCompartivo" role="tabpanel" aria-labelledby="listCompartivo-tab">
                    <div class="p-0 overflow-x-auto ps ps--active-x">
                        <table class="items-center w-full mb-0 align-top border-collapse dark:border-white/40 text-slate-500">
                            <x-table-header :headers="$headerComparativo" />
                            <tbody id="tableComparativo"></tbody>
                        </table>
                        <div id="noDataMessageComparativo" class="hidden text-center text-gray-500">
                            No hay datos disponibles.
                        </div>
                    </div>
                </div>
    
                <div class="hidden p-4 rounded-lg bg-white dark:bg-gray-800" id="styled-grafica" role="tabpanel" aria-labelledby="grafica-tab">
                    <div class="w-full px-2">
                        <select id="tipoGrafico" class="mt-4 border border-gray-300 rounded px-2 py-1">
                            <option value="1">Materiales x Cantidades</option>
                            <option value="2">Materiales x Valor</option>
                        </select>
                        <canvas id="graficaComparativa" class="hidden" width="1200" height="800" style="width: 600px; height: 400px;"></canvas>
                    </div>
                </div>

            </div>
        </div>

        <div class="flex justify-start">
            <a href="#" tabindex="0"
                x-on:click="$dispatch('close-modal', 'compartivo-modal')"
                class="bg-red-500 text-white px-4 py-2 rounded"
            >
                Cerrar
            </a>
        </div>
    </div>
</x-modal>