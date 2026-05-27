<!-- Modal -->
<x-modal name="modalItemsArea-modal" maxWidth="6xl">
    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div class="border-b pb-2">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                Selecciona un área y agrega sus ítems
            </h2>
        </div>

        <form id="formItemsArea" class="space-y-4">

            {{-- Área + botón --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 items-end">

                <div class="md:col-span-2">
                    <x-input-label for="id_area" :value="__('Seleccione Área *')" />
                    <x-select-input
                        name="id_area"
                        id="id_area"
                        :datax="true"
                        :data="['id', 'nombre_area']"
                        :options="$areas"
                        class="block mt-1 w-full"
                        required
                    />
                </div>

                <div class="flex justify-end">
                    <x-secondary-button
                        id="addItem"
                        class="flex items-center gap-2 px-4 py-2"
                    >
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-width="2" d="M12 5v14m7-7H5"/>
                        </svg>
                        <span>Agregar ítem</span>
                    </x-secondary-button>
                </div>

            </div>

            {{-- Items --}}
            <div id="area-items" class="space-y-2">

                <div class="item-group border rounded-xl p-2 shadow-sm bg-gray-50" data-item-number="1">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-md font-bold text-gray-700 dark:text-gray-300">Item 1</h3>
                        <button type="button" class="remove-item text-white bg-red-500 px-2 py-1 rounded-lg">✕</button>
                    </div>

                    {{-- Layout principal --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">

                        <div class="md:col-span-1 flex flex-col gap-2">
                            <div class="flex-1">
                                <div class="">
                                    <x-input-label for="unidad" :value="__('Unidades *')" />
                                    <x-select-input
                                        name="unidad"
                                        id="unidad"
                                        :datax="true"
                                        :options="$unidades"
                                        class="block mt-1 w-full"
                                        required
                                    />
                                </div>
                                <div>
                                    <x-input-label for="cantidad" :value="__('Cantidad *')" />
                                    <x-text-input
                                        id="cantidad"
                                        name="cantidad"
                                        type="number"
                                        step="any"
                                        step="0.01"
                                        class="block mt-1 w-full"
                                        required
                                    />
                                </div>
                            </div>

                            <div class="relative">
                                <x-input-label for="valor" :value="__('Valor Unidad *')" />
                                <x-text-input
                                    id="valor"
                                    name="valor"
                                    type="number"
                                    min="0"
                                    class="block mt-1 w-full moneda-cop"
                                    required
                                />
                            </div>
                        </div>

                        {{-- Columna derecha (textarea ocupa toda la altura) --}}
                        <div class="md:col-span-2 flex flex-col">
                            <x-input-label :value="__('Descripción *')" />
                            <textarea
                                name="items[]"
                                required
                                class="block w-full mt-1 flex-1 min-h-[110px] rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                            ></textarea>
                        </div>

                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="flex justify-between items-center pt-4 border-t">
                <button type="button"
                    x-on:click="$dispatch('close-modal', 'modalItemsArea-modal')"
                    class="bg-red-500 text-white px-4 py-2 rounded"
                >
                    Cancelar
                </button>
                <x-primary-button>
                    Agregar
                </x-primary-button>
            </div>

        </form>
    </div>
</x-modal>
