<x-modal name="modalPagos-modal" maxWidth="4xl">
    <div class="p-6" x-data>

        <h2 id="txTitulo" class="text-2xl font-bold mb-4 text-gray-800 text-center"></h2>

        <form id="formPago" method="POST" action="{{ route('cartera.save') }}" class="">
            @csrf
            <div class="flex flex-wrap border border-gray-200 m-2 rounded-lg">
                <input type="hidden" id="proyecto_id" name="proyecto_id">
                <input type="hidden" id="tipo" name="tipo">

                <div id="id_concepto" class="w-full max-w-full p-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                    <x-input-label for="concepto" :value="__('Concepto de Pago *')" />
                    <x-select-input 
                        name="concepto" 
                        id="concepto"
                        class="block mt-1 w-full" required />
                    <div data-error-for="concepto" class="mt-2 text-sm text-red-600 hidden"></div>
                </div>

                <div class="w-full max-w-full p-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                    <x-input-label for="fecha_pago" :value="__('Fecha de Pago *')" />
                    <div class="relative w-full mt-1">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                            </svg>
                        </div>
                        <input 
                            datepicker=""
                            datepicker-format="yyyy-mm-dd"
                            autocomplete="off"
                            type="text" 
                            id="fecha_pago" 
                            name="fecha_pago" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                            placeholder="Seleccione fecha"
                            value="{{ now() }}" 
                            required 
                        />
                        <div data-error-for="fecha_pago" class="mt-2 text-sm text-red-600 hidden"></div>
                    </div>
                </div>

                <div class="w-full max-w-full p-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                    <x-input-label for="fv" :value="__('Factura de Venta')" />
                    <x-text-input id="fv" maxlength="20" class="block mt-1 w-full" type="text" name="fv" />
                    <div data-error-for="fv" class="mt-2 text-sm text-red-600 hidden"></div>
                </div>

                <div class="w-full max-w-full p-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                    <x-input-label for="valor_pagado" :value="__('Valor Pagado *')" />
                    <x-text-input id="valor_pagado" min="0" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-300 focus:border-blue-400 moneda-cop"
                        type="number" name="valor_pagado" placeholder="Ej: 200000" required/>
                        <div data-error-for="valor_pagado" class="mt-2 text-sm text-red-600 hidden"></div>
                </div>

                <div class="w-full p-2 md:w-6/12 lg:w-9/12 2xl:w-9/12">
                    <x-input-label for="comentario" :value="__('Comentario')" />
                    <textarea id="comentario" name="comentario"
                        class="block w-full mt-1 h-20 rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                        placeholder="Agrega una observación si lo deseas..."></textarea>
                </div>

                <!-- Botones -->
                <div class="flex w-full justify-end p-2">
                    <button form="formPago" type="submit"
                        class="bg-green-600 text-white px-6 py-2.5 rounded-lg shadow hover:bg-green-700 focus:ring-4 focus:ring-green-300 transition-all">
                        Guardar Pago
                    </button>
                </div>
            </div>
        </form>

        <!-- Tabla responsive -->
        <div id="tablaPagos" class="overflow-x-auto rounded-xl border border-gray-200 shadow-md bg-white overflow-y-hidden"></div>

        <!-- Botones -->
        <div class="flex justify-end pt-2">
            <button type="button"
                    x-on:click="$dispatch('close-modal', 'modalPagos-modal')"
                    class="bg-red-500 text-white px-5 py-2.5 rounded-lg shadow hover:bg-red-600 focus:ring-4 focus:ring-red-300 transition-all">
                Cerrar
            </button>
        </div>

    </div>
</x-modal>
