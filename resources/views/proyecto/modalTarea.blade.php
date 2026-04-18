<!-- Modal -->
<x-modal name="my-modal" maxWidth="4xl">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-4" id="TextModalTarea" ></h2>
        <div class="w-full px-2">
            <form method="POST" action="{{ route('proyecto.saveTarea') }}">
                @csrf
                <div class="flex flex-wrap -mx-3">
                    <input type="hidden" id="id" name="id" >
                    <input type="hidden" id="id_proyecto" name="id_proyecto">

                    <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                        <x-input-label for="id_user" :value="__('Usuario Encargado *')" />
                        <x-select-input
                            name="id_user"
                            id="id_user"
                            :options="$userColab"
                            :selected="old('id_user')"
                            class="block mt-1 w-full"
                            :data="['id', 'nombre_completo']"
                        />
                        <x-input-error :messages="$errors->get('id_user')" class="mt-2" />
                    </div>

                    <input type="hidden" id="id_tarea_estado" name="id_tarea_estado" value="{{ old('id_tarea_estado') }}">
                    <!--<div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                        <x-input-label for="id_tarea_estado" :value="__('Estado Tarea *')" />
                        <x-select-input
                            name="id_tarea_estado"
                            id="id_tarea_estado"
                            :options="$estadoTarea"
                            :selected="old('id_tarea_estado')"
                            class="block mt-1 w-full"
                        />
                        <x-input-error :messages="$errors->get('id_tarea_estado')" class="mt-2" />
                    </div>-->
                    <input type="hidden" id="id_tarea_tipo" name="id_tarea_tipo" value="{{ old('id_tarea_tipo') }}">
                    <!--<div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                        <x-input-label for="id_tarea_tipo" :value="__('Tipo *')" />
                        <x-select-input
                            name="id_tarea_tipo"
                            id="id_tarea_tipo"
                            :options="$tareaTipo"
                            :data="['id', 'nombre_tarea']"
                            :selected="old('id_tarea_tipo')"
                            class="block mt-1 w-full"
                        />
                        <x-input-error :messages="$errors->get('id_tarea_tipo')" class="mt-2" />
                    </div>-->
                    <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                        <x-input-label for="fec_inicio" :value="__('Fecha Inicio *')" />
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
                                id="fec_inicio"
                                name="fec_inicio"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full
                                ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Selecciona una fecha"
                                value="{{ old('fec_inicio', now()) }}"
                                required
                            />
                        </div>
                        <x-input-error :messages="$errors->get('fec_inicio')" class="mt-2" />
                    </div>


                    <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                        <label class="inline-flex items-center me-5 cursor-pointer py-7 px-2">
                            <input type="hidden" name="conFechaFin" value="0">
                            <input id="conFechaFin" name="conFechaFin" type="checkbox" value="1"
                                   class="sr-only peer"
                                   @checked(old('conFechaFin'))>
                            <div class="relative w-11 h-6 bg-gray-200 rounded-full peer-focus:ring-4 peer-focus:ring-purple-300 peer-checked:bg-purple-600 after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                            <span class="ms-3 text-sm font-medium text-gray-900">Con Fecha Comision</span>
                        </label>
                    </div>
                    <div id="fechaFinContainer" class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                        <x-input-label for="fec_fin" :value="__('Fecha Fin *')" />
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
                                id="fec_fin"
                                name="fec_fin"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Selecciona una fecha"
                                value="{{ old('fec_fin', now()) }}"
                                required
                            />
                        </div>
                        <x-input-error :messages="$errors->get('fec_fin')" class="mt-2" />
                    </div>
                    <div id="diasTrabajoContainer" class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                        <x-input-label for="dias_trabajo" :value="__('Dias Duración da la Tarea *')" />
                        <x-text-input id="dias_trabajo" class="block mt-1 w-full" type="number" name="dias_trabajo"
                        :value="old('dias_trabajo', 1)"/>
                        <x-input-error :messages="$errors->get('dias_trabajo')" class="mt-2" />
                    </div>
                    <div id="idFecFinReal"  class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 hidden">
                        <x-input-label for="fec_fin_real" :value="__('Fecha Entrega')" />
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
                                id="fec_fin_real"
                                name="fec_fin_real"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full
                                ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Selecciona una fecha"
                                value="{{ old('fec_fin_real') }}"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('fec_fin_real')" class="mt-2" />
                    </div>
                    <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-6/12 2xl:w-6/12 md:flex-0">
                        <x-input-label for="descripccion" :value="__('Observacion')" />
                        <textarea
                            id="descripccion"
                            name="descripccion"
                            autofocus
                            oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px';"
                            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600
                            focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full overflow-hidden resize-none leading-6 py-2 h-11"
                            >
                            {{old('descripccion')}}
                        </textarea>
                        <x-input-error :messages="$errors->get('descripccion')" class="mt-2" />
                    </div>
                </div>
                <div class="flex justify-between">
                    <a  href="#" tabindex="0"
                        x-on:click="$dispatch('close-modal', 'my-modal')"
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
</x-modal>
