<!-- Modal -->
<x-modal name="cotizacion-modal" maxWidth="4xl">
    <div class="p-6">
        <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="cotizacion-default-styled-tab" 
                data-tabs-toggle="#cotizacion-default-styled-tab-content" data-tabs-active-classes="text-purple-600 hover:text-purple-600 dark:text-purple-500 dark:hover:text-purple-500 border-purple-600 dark:border-purple-500" 
                data-tabs-inactive-classes="dark:border-transparent text-gray-500 hover:text-gray-600 dark:text-gray-400 border-gray-100 hover:border-gray-300 dark:border-gray-700 dark:hover:text-gray-300" role="tablist">
                <li class="me-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="cotizacion-profile-styled-tab" data-tabs-target="#cotizacion-styled-profile" type="button" role="tab" 
                    aria-controls="profile" aria-selected="false">Lista Cotizacion</button>
                </li>
                <li role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="contacts-styled-tab" 
                    data-tabs-target="#cotizacion-styled-contacts" type="button" role="tab" 
                    aria-controls="contacts" aria-selected="false">Crear Cotizacion</button>
                </li>
            </ul>
        </div>
        <div id="cotizacion-default-styled-tab-content">
            <div class="hidden p-4 rounded-lg bg-white dark:bg-gray-800" id="cotizacion-styled-profile" role="tabpanel" aria-labelledby="profile-tab">
                <h2 class="text-xl font-semibold mb-4">Lista Cotizacion</h2>
                <div class="p-0 overflow-x-auto ps ps--active-x">
                    <table class="items-center w-full mb-0 align-top border-collapse dark:border-white/40 text-slate-500">
                        <x-table-header :headers="$headerCotizacion" />
                        <tbody id="listaCotizacion"></tbody>
                    </table>
                    <!-- Paginador -->
                    <div id="paginationCotizacion" class="flex justify-center mt-4">
                        <!-- Aquí se cargará la paginación -->
                    </div>
                    <div id="noDataMessageCotizacion" class="hidden text-center text-gray-500">
                        No hay datos disponibles.
                    </div>
                </div>
                <div class="flex w-full justify-end p-2">
                    <spam id="totalCotizacion" class="span-red font-medium text-lg"></spam>
                </div>

                <div class="flex justify-star">
                    <button
                        x-data
                        x-on:click="$nextTick(() => document.getElementById('cotizacion-profile-styled-tab').click());
                            $dispatch('close-modal', 'cotizacion-modal')"
                        class="bg-red-500 text-white px-4 py-2 rounded"
                    >
                        Cerrar
                    </button>
                </div>
            </div>

            <div class="hidden p-4 rounded-lg bg-white dark:bg-gray-800" id="cotizacion-styled-contacts" role="tabpanel" aria-labelledby="contacts-tab">
                <h2 class="text-xl font-semibold mb-4">Crear Cotizacion</h2>
                <div class="w-full p-2 max-h-full overflow-y-scroll">
                    <form method="POST" id="formCotizacion" action="{{ route('proyecto.saveCotizacion') }}">
                        @csrf
                        <div class="flex flex-col lg:flex-row gap-4 mb-3">
                            <!-- Primera columna -->
                            <div class="w-full lg:w-1/2 space-y-4 p-3 border-2 border-gray-400 rounded-2xl">
                                <input type="hidden" id="id_proyecto_cotizacion" name="id_proyecto_cotizacion" >
                                <div>
                                    <input class="hidden" value="{{ json_encode($materiales) }}"  id="arrayMateriales" name="arrayMateriales" disabled>
                                    <x-input-label for="id_material" :value="__('Seleccione Material *')" />
                                    <x-select-input 
                                        placeholder="Busqueda.."
                                        autocomplete="off"
                                        name="id_material" 
                                        id="id_material"
                                        :options="$materiales" 
                                        :data="['id', 'nombre_material']"
                                        :selected="old('id_material')" 
                                        class="block mt-1 w-full"
                                    />
                                    <x-input-error :messages="$errors->get('id_material')" class="mt-2" />
                                </div>
                            </div>
            
                            <!-- Segunda columna -->
                            <div class="w-full h-full lg:w-1/2 text-center border-2 border-gray-400 rounded-2xl">
                                <p class="font-bold text-xl mb-3">Materiales Pedidos</p>
                                <div id="selectMateriales"></div>
                            </div>
                        </div>
            
                        <!-- Botón alineado a la derecha siempre en la parte inferior -->
                        <div class="flex justify-between">
                            <a
                                x-data
                                x-on:click="$nextTick(() => document.getElementById('cotizacion-profile-styled-tab').click());
                                $dispatch('close-modal', 'cotizacion-modal')"
                                class="bg-red-500 text-white px-4 py-2 rounded"
                            >
                                Cerrar
                            </a>
                            <x-primary-button class="ms-4">
                                Guardar
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-modal>