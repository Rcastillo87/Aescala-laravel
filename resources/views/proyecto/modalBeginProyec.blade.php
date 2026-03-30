<!-- Modal -->
<x-modal name="beginProyec-modal" maxWidth="4xl">
    <div class="p-6">
        <h2 class="mb-3 text-xl font-semibold">Datos del Proyecto</h2>

        <div class="flex w-full flex-wrap border border-gray-200 rounded-lg p-2 mb-4">
            <div class="mb-1 w-full max-w-full shrink-0 pl-2 md:w-6/12 md:flex-0 lg:w-4/12 2xl:w-3/12">
                <p class="text-lg font-bold text-gray-500">Nombre Proyecto</p>
                <span class="text-md text-black" id="txNombreProyec" ></span>
            </div>
            <div class="mb-1 w-full max-w-full shrink-0 pt-1 pl-2 md:w-6/12 md:flex-0 lg:w-4/12 2xl:w-3/12">
                <p class="text-lg font-bold text-gray-500">DPT - Ciudad</p>
                <span class="text-md text-black" id="txUbicacion" ></span>
            </div>
            <div class="mb-1 w-full max-w-full shrink-0 pt-1 pl-2 md:w-6/12 md:flex-0 lg:w-4/12 2xl:w-3/12">
                <p class="text-lg font-bold text-gray-500">Dirrecion</p>
                <span class="text-md text-black" id="txDireccion"></span>
            </div>
            <div class="mb-1 w-full max-w-full shrink-0 pt-1 pl-2 md:w-6/12 md:flex-0 lg:w-4/12 2xl:w-3/12">
                <p class="text-lg font-bold text-gray-500" >Nombre Cliente</p>
                <span class="text-md text-black" id="txContacto"></span>
            </div>
            <div class="mb-1 w-full max-w-full shrink-0 pt-1 pl-2 md:w-6/12 md:flex-0 lg:w-4/12 2xl:w-3/12">
                <p class="text-lg font-bold text-gray-500">Documento Cliente</p>
                <span class="text-md text-black" id="txDocumento"></span>
            </div>
            <div class="mb-1 w-full max-w-full shrink-0 pt-1 pl-2 md:w-6/12 md:flex-0 lg:w-4/12 2xl:w-3/12">
                <p class="text-lg font-bold text-gray-500">Telefono Cliente</p>
                <span class="text-md text-black" id="txTelefono"></span>
            </div>
            <div class="mb-1 w-full max-w-full shrink-0 pt-1 pl-2 md:w-6/12 md:flex-0 lg:w-4/12 2xl:w-3/12">
                <p class="text-lg font-bold text-gray-500">Area Privada(MT<sup>2</sup>)</p>
                <span class="text-md text-black" id="txAreaPrivada"></span>
            </div>
            <div class="mb-1 w-full max-w-full shrink-0 pt-1 pl-2 md:w-6/12 md:flex-0 lg:w-4/12 2xl:w-3/12">
                <p class="text-lg font-bold text-gray-500">Dias Duracion Proyecto</p>
                <span class="text-md text-black" id="txDiasProyecto"></span>
            </div>
        </div>

        <h2 class="mb-3 text-xl font-semibold">Inicia Proyecto</h2>

        <form method="POST" id="formBeginProyec" action="{{ route('proyecto.begin') }}">
            @csrf
            <div class="flex flex-wrap border border-gray-200 rounded-lg">
                <input type="hidden" name="id_proyecto_begin" id="id_proyecto_begin">

                <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                    <x-input-label for="fec_inicio_begin" :value="__('Fecha Ini Proyecto *')" />
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
                            type="text"
                            id="fec_inicio_begin"
                            name="fec_inicio_begin"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Seleccione fecha"
                            value="{{ now() }}"
                            required
                        />
                    </div>
                    <x-input-error :messages="$errors->get('fec_inicio')" class="mt-2" />
                </div>

                <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                    <label class="inline-flex items-center me-5 cursor-pointer py-7 px-2">
                        <input type="hidden" name="conFechaFin_b" id="conFechaFin_b" value="0">
                        <input id="checkboxFecha" name="checkboxFecha" type="checkbox"
                            class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 rounded-full peer-focus:ring-4 peer-focus:ring-purple-300 peer-checked:bg-purple-600 after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                        <span class="ms-3 text-sm font-medium text-gray-900">Con Fecha Fin</span>
                    </label>
                </div>
                <div id="fechaFinBegin" class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 hidden">
                    <x-input-label for="fec_fin_estimado_b" :value="__('Fecha Fin Estimado *')" />
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                            </svg>
                        </div>
                        <input datepicker datepicker-format="yyyy-mm-dd" type="text"
                                id="fec_fin_estimado_b" name="fec_fin_estimado_b"
                                datepicker
                                datepicker-format="yyyy-mm-dd"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                                value="{{ old('fec_fin_estimado_b', now()) }}"
                                placeholder="Seleccione fecha">
                    </div>
                    <x-input-error :messages="$errors->get('fec_fin')" class="mt-2" />
                </div>
                <div id="diasTrabajoBegin" class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                    <x-input-label for="dias_trabajo_begin" :value="__('Días Duración del Proyecto *')" />
                    <x-text-input id="dias_trabajo_begin" class="block w-full" type="number"
                                    name="dias_trabajo_begin" value="{{ old('dias_trabajo_begin') }}"/>
                    <x-input-error :messages="$errors->get('dias_trabajo_begin', 1)" class="mt-2" />
                </div>

                <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                    <x-input-label for="id_user" :value="__('Arquitecto Encargado *')" />
                    <x-select-input
                        name="id_user_proy"
                        id="id_user_proy"
                        :options="$colaUsers"
                        :data="['id', 'nombre_completo']"
                        :selected="old('id_user')"
                        class="block mt-1 w-full"
                        required
                    />
                    <x-input-error :messages="$errors->get('id_user')" class="mt-2" />
                </div>

                <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                    <x-input-label for="id_user_obra_blanca" :value="__('Cont. Obra Blanca')" />
                    <x-select-input
                        name="id_user_obra_blanca"
                        id="id_user_obra_blanca"
                        :options="$contraUsers"
                        :data="['id', 'nombre_completo']"
                        :selected="old('id_user_obra_blanca')"
                        class="block mt-1 w-full"
                    />
                    <x-input-error :messages="$errors->get('id_user_obra_blanca')" class="mt-2" />
                </div>

                <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                    <x-input-label for="id_user_carpinteria" :value="__('Cont. Carpinteria')" />
                    <x-select-input
                        name="id_user_carpinteria"
                        id="id_user_carpinteria"
                        :options="$contraUsers"
                        :data="['id', 'nombre_completo']"
                        :selected="old('id_user_carpinteria')"
                        class="block mt-1 w-full"
                    />
                    <x-input-error :messages="$errors->get('id_user_carpinteria')" class="mt-2" />
                </div>

                <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                    <label class="inline-flex items-center me-5 cursor-pointer py-7 px-2">
                        <input type="hidden" name="conFechaDise" id="conFechaDise" value="0">
                        <input id="checkboxFechaDise" name="checkboxFechaDise" type="checkbox"
                            class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 rounded-full peer-focus:ring-4 peer-focus:ring-purple-300 peer-checked:bg-purple-600 after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                        <span class="ms-3 text-sm font-medium text-gray-900">Con Fecha Diseño</span>
                    </label>
                </div>
                <div id="fechaDiseDiv" class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 hidden">
                    <x-input-label for="fec_ini_dise" :value="__('Fecha Inicio Diseño *')" />
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                            </svg>
                        </div>
                        <input datepicker datepicker-format="yyyy-mm-dd" type="text"
                                id="fec_ini_dise" name="fec_ini_dise"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                                value="{{ old('fec_ini_dise', now()) }}"
                                placeholder="Seleccione fecha"
                                datepicker
                                datepicker-format="yyyy-mm-dd"
                                disabled>
                    </div>
                    <x-input-error :messages="$errors->get('fec_ini_dise')" class="mt-2" />
                </div>

                <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                    <x-input-label for="ubicacion" :value="__('Ubicación *')" />
                    <x-select-input
                        name="ubicacion"
                        id="ubicacion"
                        :options="$ubicacion"
                        class="block mt-1 w-full"
                        required
                    />
                    <x-input-error :messages="$errors->get('ubicacion')" class="mt-2" />
                </div>

                <div class="w-full max-w-full p-3 shrink-0 lg:w-8/12 2xl:w-6/12 md:flex-0">
                    <x-input-label for="observacion" :value="__('Comentario')" />
                    <textarea id="observacion" class="block mt-1 w-full h-20" type="text" name="observacion" autofocus></textarea>
                    <x-input-error :messages="$errors->get('observacion')" class="mt-2" />
                </div>

                <div class="flex w-full justify-between m-2">
                    <a  href="#" tabindex="0"
                        x-on:click="$dispatch('close-modal', 'beginProyec-modal')"
                        class="bg-red-500 text-white px-4 py-2 rounded"
                    >
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
