<!-- MODAL PARA AÑADIR COMENTARIO Y CAMBIAR ESTADO (3 o 4) -->
<x-modal name="modal-comentario" maxWidth="lg" :closeOnOutsideClick="false" :closeOnEscape="false">
    <div class="p-6 dark:bg-gray-800">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Gestionar Novedad y Respuesta
        </h2>

        <form id="formComentario" onsubmit="enviarComentario(event)">
            @csrf
            <input type="hidden" id="novedad_id" name="novedad_id">

            <!-- Selector Estado 3 y 4 -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nuevo Estado *</label>
                <select name="estado" id="select_estado_nuevo" required
                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm">
                    <option value="">Seleccione...</option>
                    <option value="3">Aceptado</option>
                    <option value="4">Rechazado/Cancelado</option>
                    <option value="5">Parcial</option>
                </select>
            </div>

            <!-- Comentario Obligatorio -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Comentario *</label>
                <textarea name="comentario" id="textarea_comentario" rows="4" required
                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                    placeholder="Escribe el motivo o comentario..."></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'modal-comentario')"
                    class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</x-modal>