<!-- Modal -->
<x-modal name="avance-modal" maxWidth="4xl">
    <div class="p-6">
        <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="avance-default-styled-tab" 
                data-tabs-toggle="#avance-default-styled-tab-content" data-tabs-active-classes="text-purple-600 hover:text-purple-600 dark:text-purple-500 dark:hover:text-purple-500 border-purple-600 dark:border-purple-500" 
                data-tabs-inactive-classes="dark:border-transparent text-gray-500 hover:text-gray-600 dark:text-gray-400 border-gray-100 hover:border-gray-300 dark:border-gray-700 dark:hover:text-gray-300" role="tablist">
                <li class="me-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="avance-profile-styled-tab" data-tabs-target="#avance-styled-profile" type="button" role="tab" 
                    aria-controls="profile" aria-selected="false">Lista Avance</button>
                </li>
                <li role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" 
                    id="avance-contacts-styled-tab" 
                    data-tabs-target="#avance-styled-contacts" type="button" role="tab" aria-controls="contacts" aria-selected="false">Crear Avance</button>
                </li>
            </ul>
        </div>
        <div id="avance-default-styled-tab-content">
            <div class="hidden p-4 rounded-lg bg-white dark:bg-gray-800" id="avance-styled-profile" role="tabpanel" aria-labelledby="profile-tab">
                <h2 class="text-xl font-semibold mb-4">Lista Avances</h2>
                <div class="p-0 overflow-x-auto ps ps--active-x">
                    <table class="items-center w-full mb-0 align-top border-collapse dark:border-white/40 text-slate-500">
                        <x-table-header :headers="$headerAvance" />
                        <tbody id="avanceList"></tbody>
                    </table>
                    <!-- Paginador -->
                    <div id="paginationAvance" class="flex justify-center mt-4">
                        <!-- Aquí se cargará la paginación -->
                    </div>
                    <div id="noDataMessageAvance" class="hidden text-center text-gray-500">
                        No hay datos disponibles.
                    </div>
                </div>
                <div class="flex justify-star">
                    <a  href="#" tabindex="0"
                        x-data
                        x-on:click="$nextTick(() => document.getElementById('avance-profile-styled-tab').click());
                            $dispatch('close-modal', 'avance-modal')"
                        class="bg-red-500 text-white px-4 py-2 rounded"
                    >
                        Cerrar
                </a>
                </div>
            </div>

            <div class="hidden p-4 rounded-lg bg-white dark:bg-gray-800" id="avance-styled-contacts" role="tabpanel" aria-labelledby="contacts-tab">
                <h2 class="text-xl font-semibold mb-4">Crear Avance</h2>
                <div class="w-full px-2">
                    <form method="POST" action="{{ route('proyecto.saveAvance') }}">
                        @csrf
                        <input type="hidden" id="id_tarea_avance" name="id_tarea_avance" >
                        <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-6/12 2xl:w-6/12 md:flex-0">
                            <x-input-label for="avance" :value="__('Describa el Avance *')" />
                            <x-text-input id="avance" class="block mt-1 w-full" type="text" name="avance" :value="old('avance')" 
                            required autofocus />
                            <x-input-error :messages="$errors->get('avance')" class="mt-2" />
                        </div>
                        <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                            <x-input-label for="fec_avance" :value="__('Fecha del Avance *')" />
                            <div class="relative max-w-sm">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                    </svg>
                                </div>
                                <input 
                                    datepicker=""
                                    datepicker-format="yyyy-mm-dd"
                                    autocomplete="off"
                                    id="fec_avance" 
                                    name="fec_avance" 
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                    placeholder="Selecciona una fecha"
                                    value="{{ old('fec_avance', now()) }}" 
                                    required 
                                />
                            </div>
                            <x-input-error :messages="$errors->get('fec_avance')" class="mt-2" />
                        </div>
                        <div class="flex justify-between">
                            <a
                                href="#" tabindex="0"
                                x-data
                                x-on:click="$nextTick(() => document.getElementById('avance-profile-styled-tab').click());
                                $dispatch('close-modal', 'avance-modal')"
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