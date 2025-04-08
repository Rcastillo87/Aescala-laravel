<!-- Modal -->
<x-modal name="my-modal" maxWidth="4xl">
    <div class="p-6">
        <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-styled-tab" 
                data-tabs-toggle="#default-styled-tab-content" data-tabs-active-classes="text-purple-600 hover:text-purple-600 dark:text-purple-500 dark:hover:text-purple-500 border-purple-600 dark:border-purple-500" 
                data-tabs-inactive-classes="dark:border-transparent text-gray-500 hover:text-gray-600 dark:text-gray-400 border-gray-100 hover:border-gray-300 dark:border-gray-700 dark:hover:text-gray-300" role="tablist">
                <li class="me-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-styled-tab" data-tabs-target="#styled-profile" type="button" role="tab" 
                    aria-controls="profile" aria-selected="false">Lista de Prestamos y Devoluciones</button>
                </li>
                <li role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="contacts-styled-tab" 
                    data-tabs-target="#styled-contacts" type="button" role="tab" aria-controls="contacts" aria-selected="false">Prestamos y Devolucion</button>
                </li>
            </ul>
        </div>
        <div id="default-styled-tab-content">
            <div class="hidden p-4 rounded-lg bg-white dark:bg-gray-800" id="styled-profile" role="tabpanel" aria-labelledby="profile-tab">
                <h2 class="text-xl font-semibold mb-4">Lista de Prestamos y Devoluciones</h2>
                <div class="p-0 overflow-x-auto ps ps--active-x">
                    <table class="items-center w-full mb-0 align-top border-collapse dark:border-white/40 text-slate-500">
                        <x-table-header :headers="$headersPrestamos" />
                        <tbody id="serviceList"></tbody>
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
                            $dispatch('close-modal', 'my-modal')"
                        class="bg-red-500 text-white px-4 py-2 rounded"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
            <div class="hidden p-4 rounded-lg bg-white dark:bg-gray-800" id="styled-contacts" role="tabpanel" aria-labelledby="contacts-tab">
                <h2 class="text-xl font-semibold mb-4">Prestamos y Devolucion</h2>
                <div class="w-full px-2">
                    <form method="POST" action="{{ route('herramienta.savePrestamo') }}">
                        @csrf
                        <div class="flex flex-wrap -mx-3">
                            <input type="hidden" id="id" name="id" >
                            <input type="hidden" id="id_herramienta" name="id_herramienta" >
                            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                                <x-input-label for="id_user" :value="__('Usuario Presta o Devuelve *')" />
                                <x-select-input 
                                    name="id_user" 
                                    :options="$userPrestamo" 
                                    :selected="old('id_user')" 
                                    class="block mt-1 w-full" 
                                    :data="['id', 'nombre_completo']"
                                />
                                <x-input-error :messages="$errors->get('id_user')" class="mt-2" />
                            </div>
                            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                                <x-input-label for="tipo_prestamo" :value="__('Presta o Devuelve *')" />
                                <x-select-input 
                                    name="tipo_prestamo" 
                                    :options="$prestamo" 
                                    :selected="old('tipo_prestamo')" 
                                    class="block mt-1 w-full" 
                                />
                                <x-input-error :messages="$errors->get('tipo_prestamo')" class="mt-2" />
                            </div>
                            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                                <x-input-label for="fec_prestamo" :value="__('Fecha Presta o Devuelve *')" />
                                <div class="relative max-w-sm">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                        </svg>
                                    </div>
                                    <input 
                                        data-datepicker 
                                        data-datepicker-format="yyyy-mm-dd"
                                        datepicker-max-date="{{ date('Y-m-d') }}" 
                                        type="text" 
                                        id="fec_prestamo" 
                                        name="fec_prestamo" 
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                        placeholder="Selecciona una fecha" 
                                        value="{{ old('fec_prestamo', date('Y-m-d')) }}" 
                                        required 
                                    />
                                </div>
                                <x-input-error :messages="$errors->get('fec_prestamo')" class="mt-2" />
                            </div>
                            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-6/12 2xl:w-6/12 md:flex-0">
                                <x-input-label for="observacion" :value="__('Observacion *')" />
                                <x-text-input id="observacion" class="block mt-1 w-full" type="text" name="observacion" :value="old('observacion')" 
                                required autofocus />
                                <x-input-error :messages="$errors->get('observacion')" class="mt-2" />
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <button
                                x-data
                                x-on:click="$nextTick(() => document.getElementById('profile-styled-tab').click());
                                $dispatch('close-modal', 'my-modal')"
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