<!-- Modal -->
<x-modal name="modalOtrosi-modal" maxWidth="4xl">
    <div class="p-6">

        <h2 class="mb-3 text-xl font-semibold" id="txLabelOtroSi">Crear Otro Si</h2>

        <form method="POST" id="formOtroSi" action="{{ route('otro_si.save') }}">
            @csrf
            <div class="flex flex-wrap border border-gray-200 rounded-lg">


                <div class="flex w-full items-center">
                    <!-- Botón -->
                    <div class="p-3 shrink-0">
                        <a class="inline-flex items-center py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150 py-1 px-1"
                            id="addItem" href="#" data-tooltip-target="tooltip-hover-item">
                            <svg class="w-6 h-6 text-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M5 12h14m-7 7V5"></path>
                            </svg>
                        </a>
                        <div id="tooltip-hover-item" role="tooltip"
                            class="absolute z-10 px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs tooltip dark:bg-gray-700 opacity-0 invisible"
                            style="position: absolute; inset: auto auto 0px 0px; margin: 0px; transform: translate(23px, -142px);"
                            data-popper-placement="top">
                            Añadir item
                            <div class="tooltip-arrow" data-popper-arrow=""
                                style="position: absolute; left: 0px; transform: translate(32px, 0px);"></div>
                        </div>
                    </div>

                    <!-- Input -->
                    <div id="entregable-items" class="w-full">
                        <!-- Item base que usaremos como plantilla -->
                        <div class="item-group flex items-center gap-4 w-full px-3 mb-2" data-item-number="1">
                            <label
                                class="block font-medium text-sm text-gray-700 dark:text-gray-300 item-label whitespace-nowrap">
                                Item 1 *
                            </label>
                            <input required=""
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm item-input block w-full"
                                type="text" name="items[]">
                            <a href="#" class="remove-item bg-red-500 text-white px-4 py-2 rounded">
                                <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                

                <div class="flex w-full justify-between m-2">
                    <a href="#" tabindex="0" x-on:click="$dispatch('close-modal', 'modalOtrosi-modal')"
                        class="bg-red-500 text-white px-4 py-2 rounded">
                        Cerrar
                    </a>
                    <x-primary-button class="ms-4">
                        Guardar
                    </x-primary-button>
                </div>
            </div>
        </form>

    </div>
</x-modal>
