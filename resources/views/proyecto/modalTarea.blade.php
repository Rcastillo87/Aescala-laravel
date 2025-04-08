<!-- Modal -->
<x-modal name="my-modal" maxWidth="4xl">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-4" id="TextModalTarea" ></h2>
        <div class="w-full px-2">
            <form method="POST" action="{{ route('proyecto.saveTarea') }}">
                @csrf
                <div class="flex flex-wrap -mx-3">
                    <input type="hidden" id="id" name="id" >
                    <input type="hidden" id="id_proyecto" name="id_proyecto" >
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
                    <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                        <x-input-label for="id_tarea_estado" :value="__('Estado Tarea *')" />
                        <x-select-input 
                            name="id_tarea_estado" 
                            id="id_tarea_estado" 
                            :options="$estadoTarea" 
                            :selected="old('id_tarea_estado')" 
                            class="block mt-1 w-full" 
                        />
                        <x-input-error :messages="$errors->get('id_tarea_estado')" class="mt-2" />
                    </div>
                    <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
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
                    </div>
                    <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                        <x-input-label for="fec_inicio" :value="__('Fecha Inicio *')" />
                        <div class="relative max-w-sm">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                </svg>
                            </div>
                            <input 
                                data-datepicker 
                                data-datepicker-format="yyyy-mm-dd"
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
                        <x-input-label for="fec_fin" :value="__('Fecha Fin *')" />
                        <div class="relative max-w-sm">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                </svg>
                            </div>
                            <input 
                                data-datepicker 
                                data-datepicker-format="yyyy-mm-dd"
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
                    <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-6/12 2xl:w-6/12 md:flex-0">
                        <x-input-label for="descripccion" :value="__('Observacion *')" />
                        <x-text-input id="descripccion" class="block mt-1 w-full" type="text" name="descripccion" :value="old('descripccion')" 
                        required autofocus />
                        <x-input-error :messages="$errors->get('descripccion')" class="mt-2" />
                    </div>
                </div>
                <div class="flex justify-between">
                    <a
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