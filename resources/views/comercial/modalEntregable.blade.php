<!-- Modal -->
<x-modal name="entregable-modal" maxWidth="4xl">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-4">Anadir Entregables</h2>
        <div class="w-full px-2">
            <form class="flex flex-wrap -mx-3" id="formEntregable">

                <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                    <x-input-label for="id_entregable" :value="__('Seleccione Entregable *')" />
                    <x-select-input 
                        autocomplete="off"
                        name="id_entregable" 
                        id="id_entregable"
                        :datax="true"
                        :options="$entregables" 
                        :data="['id', 'nombre_estregable']"
                        class="block mt-1 w-full"
                        required
                    />
                </div>

                <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                    <x-input-label for="cantidad" :value="__('Cantidad *')" />
                    <x-text-input id="cantidad" class="block mt-1 w-full" step="any" type="number" min=1 name="cantidad" required/>
                </div>

                <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                    <x-input-label for="valor_total" :value="__('Valor Unidad *')" />
                    <x-text-input id="valor_total" class="block mt-1 w-full moneda-cop" step="any" min=0 type="number" name="valor_total" required/>
                </div>

                <hr class="w-full my-2">
                <div class="flex w-full items-center">
                    <!-- Botón -->
                    <div class="p-3 shrink-0">
                        <x-secondary-button class="py-1 px-1" id="addItem" href="#" data-tooltip-target="tooltip-hover-item">
                            <svg class="w-6 h-6 text-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
                            </svg>
                        </x-secondary-button>
                        <div id="tooltip-hover-item" 
                            role="tooltip" 
                            class="absolute z-10 invisible px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                            Añadir item
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </div>

                    <!-- Input -->
                    <div id="entregable-items" class="w-full">
                        <!-- Item base que usaremos como plantilla -->
                        <div class="item-group flex items-center gap-4 w-full px-3 mb-2" data-item-number="1">
                            <x-input-label class="item-label whitespace-nowrap">Item 1 *</x-input-label>
                            <x-text-input class="item-input block w-full" type="text" name="items[]" required/>
                            <a href="#" class="remove-item bg-red-500 text-white px-4 py-2 rounded">
                                <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="flex w-full justify-between mt-4">
                    <a href="#" tabindex="0"
                        x-on:click="$dispatch('close-modal', 'entregable-modal')"
                        class="bg-red-500 text-white px-4 py-2 rounded"
                    >
                        Cerrar
                    </a>
                    <x-primary-button class="ms-4">
                        Añadir
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-modal>