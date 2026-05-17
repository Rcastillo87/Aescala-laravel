<x-modal name="modalPagos-modal" maxWidth="2xl">
    <div class="p-6" x-data>

        <h2 class="text-2xl font-bold mb-4 text-gray-800 text-center">Crear abonos</h2>

        <form id="formPago" method="POST" action="{{ route('cartera.save') }}">
            @csrf
            <div class="flex flex-wrap border border-gray-200 m-2 rounded-lg">
                <input type="hidden" id="id_proyecto" name="id_proyecto" value="{{$id_proyecto}}">

                <div class="max-w-full p-2 shrink-0 w-6/12 md:flex-0">
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
                    </div>
                </div>

                <div class="max-w-full p-2 shrink-0 w-6/12 md:flex-0">
                    <x-input-label for="valor" :value="__('Valor Abonado *')" />
                    <x-text-input id="valor" min="0" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-300 focus:border-blue-400 moneda-cop"
                        type="number" name="valor" placeholder="Ej: 200000" required/>
                </div>

                <div class="max-w-full p-2 shrink-0 w-full md:flex-0">
                    <x-input-label for="comentario" :value="__('Comentario')" />
                    <textarea id="comentario" name="comentario" rows="4"
                        class="block w-full mt-1 rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                        placeholder="Agrega una observación si lo deseas..."></textarea>
                </div>

                <!-- Botones -->
                <div class="flex w-full justify-end p-2">
                    <button form="formPago" type="submit"
                        class="bg-green-600 text-white px-6 py-2.5 rounded-lg shadow hover:bg-green-700 focus:ring-4 focus:ring-green-300 transition-all">
                        Guardar Abono
                    </button>
                </div>
            </div>
        </form>
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
