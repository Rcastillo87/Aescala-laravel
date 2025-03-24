<!-- Modal -->
<x-modal name="finanza-modal" maxWidth="4xl">
    <div class="p-6">
        <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-styled-tab" 
                data-tabs-toggle="#default-styled-tab-content" data-tabs-active-classes="text-purple-600 hover:text-purple-600 dark:text-purple-500 dark:hover:text-purple-500 border-purple-600 dark:border-purple-500" 
                data-tabs-inactive-classes="dark:border-transparent text-gray-500 hover:text-gray-600 dark:text-gray-400 border-gray-100 hover:border-gray-300 dark:border-gray-700 dark:hover:text-gray-300" role="tablist">
                <li class="me-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-styled-tab" data-tabs-target="#styled-profile" type="button" role="tab" 
                    aria-controls="profile" aria-selected="false">Lista Ingresos & Egresos</button>
                </li>
                <li role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="contacts-styled-tab" 
                    data-tabs-target="#styled-contacts" type="button" role="tab" aria-controls="contacts" aria-selected="false">Crear Ingresos & Egresos</button>
                </li>
            </ul>
        </div>
        <div id="default-styled-tab-content">
            <div class="hidden p-4 rounded-lg bg-white dark:bg-gray-800" id="styled-profile" role="tabpanel" aria-labelledby="profile-tab">
                <h2 class="text-xl font-semibold mb-4">Lista Ingresos & Egresos</h2>
                <div class="p-0 overflow-x-auto ps ps--active-x">
                    <table class="items-center w-full mb-0 align-top border-collapse dark:border-white/40 text-slate-500">
                        <x-table-header :headers="$headerFinanzas" />
                        <tbody id="serviceFinanzas"></tbody>
                    </table>
                    <!-- Paginador -->
                    <div id="pagination" class="flex justify-center mt-4">
                        <!-- Aquí se cargará la paginación -->
                    </div>
                    <div id="noDataMessage" class="hidden text-center text-gray-500">
                        No hay datos disponibles.
                    </div>
                </div>
                <div class="flex justify-star">
                    <button
                        x-data
                        x-on:click="$nextTick(() => document.getElementById('profile-styled-tab').click());
                            $dispatch('close-modal', 'finanza-modal')"
                        class="bg-red-500 text-white px-4 py-2 rounded"
                    >
                        Cerrar
                    </button>
                </div>
            </div>

            <div class="hidden p-4 rounded-lg bg-white dark:bg-gray-800" id="styled-contacts" role="tabpanel" aria-labelledby="contacts-tab">
                <h2 class="text-xl font-semibold mb-4">Crear Ingresos & Egresos</h2>
                <div class="w-full px-2">
                    <form method="POST" action="{{ route('proyecto.savefinanza') }}">
                        @csrf
                        <div class="flex flex-wrap -mx-3">
                            <input type="hidden" id="id_proyecto_finanza" name="id_proyecto_finanza" >
                            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                                <x-input-label for="tipo" :value="__('Ingresos & Egresos *')" />
                                <x-select-input 
                                    name="tipo" 
                                    :options="$tipoFinanzas" 
                                    :selected="old('tipo')" 
                                    class="block mt-1 w-full" 
                                />
                                <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                            </div>
                            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                                <x-input-label for="valor" :value="__('Valor *')" />
                                <x-text-input id="valor" class="block mt-1 w-full" type="number" name="valor" :value="old('valor')" 
                                required autofocus />
                                <x-input-error :messages="$errors->get('valor')" class="mt-2" />
                            </div>
                            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-6/12 2xl:w-6/12 md:flex-0">
                                <x-input-label for="concepto" :value="__('Concepto *')" />
                                <x-text-input id="concepto" class="block mt-1 w-full" type="text" name="concepto" :value="old('concepto')" 
                                required autofocus />
                                <x-input-error :messages="$errors->get('concepto')" class="mt-2" />
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <button
                                x-data
                                x-on:click="$nextTick(() => document.getElementById('profile-styled-tab').click());
                                $dispatch('close-modal', 'finanza-modal')"
                                class="bg-red-500 text-white px-4 py-2 rounded"
                            >
                                Cerrar
                            </button>
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