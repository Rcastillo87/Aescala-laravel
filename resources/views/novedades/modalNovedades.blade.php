<x-modal name="modal-nueva-novedad" maxWidth="2xl" :closeOnOutsideClick="false" :closeOnEscape="false">
    <div class="p-6 dark:bg-gray-800">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Registrar Nuevas Novedades
        </h2>

        <form id="formNuevaNovedad" action="{{ route('novedades.saveNovedad') }}" method="POST">
            @csrf

            <!-- SELECT DE PROYECTO -->
            <div class="mb-4">
                <x-input-label for="id_proyecto" :value="__('Seleccione Proyecto *')" />
                <x-select-input 
                    placeholder="Busqueda.."
                    autocomplete="off"
                    name="id_proyecto" 
                    id="id_proyecto"
                    :options="$proyectos" 
                    :data="['id', 'nombre_proyecto']"
                    :selected="old('id_proyecto')" 
                    class="block mt-1 w-full"
                />
            </div>

            <!-- CONTENEDOR DE TEXTAREAS DINÁMICAS -->
            <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Novedades *</label>
                    <button type="button" onclick="agregarTextareaNovedad()"
                        class="text-xs px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                        + Añadir otra novedad
                    </button>
                </div>

                <div id="contenedor-novedades" class="space-y-3">
                    <!-- Textarea mínimo por defecto -->
                    <div class="flex items-start gap-2 novedad-item">
                        <textarea name="novedades_array[]" rows="2" required
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                            placeholder="Escribe la novedad..."></textarea>
                        <button type="button" onclick="eliminarTextareaNovedad(this)"
                            class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700" title="Eliminar campo">
                            🗑️
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" x-on:click="$dispatch('close-modal', 'modal-nueva-novedad')"
                    class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Guardar Novedades
                </button>
            </div>
        </form>
    </div>
</x-modal>