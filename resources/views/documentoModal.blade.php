<x-modal name="subir-documento" focusable>
    <div class="p-6" x-data="{ 
        filePreview: null, 
        isPdf: false,
        fileSelected: false,
        handleFile(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            this.fileSelected = true;
            this.isPdf = file.type === 'application/pdf';
            this.filePreview = URL.createObjectURL(file);
        }
    }">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Subir y Previsualizar Documento
        </h2>

        <form action="{{ route('documentos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Datos Polimórficos (Ejemplo: relacionando con un Usuario) -->
            <input type="hidden" name="id_tabla" value="App\Models\User">
            <input type="hidden" name="id_registro" value="1">

            <!-- Input de Archivo -->
            <div class="mb-4">
                <input type="file" name="archivo" accept="image/*,.pdf" 
                       @change="handleFile"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                       required>
            </div>

            <!-- Contenedor de Previsualización -->
            <div class="mt-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-2 min-h-[200px] flex items-center justify-center bg-gray-50 dark:bg-gray-900">
                <template x-if="!fileSelected">
                    <span class="text-gray-400">No hay archivo seleccionado</span>
                </template>

                <template x-if="fileSelected && !isPdf">
                    <img :src="filePreview" class="max-h-64 rounded shadow-sm">
                </template>

                <template x-if="fileSelected && isPdf">
                    <iframe :src="filePreview" class="w-full h-80" frameborder="0"></iframe>
                </template>
            </div>

            <!-- Botones de Acción -->
            <div class="mt-6 flex justify-end">
                <button type="button" x-on:click="$dispatch('close')" class="mr-3 text-gray-600 dark:text-gray-400">
                    Cancelar
                </button>
                
                <button type="submit" 
                        :disabled="!fileSelected"
                        :class="fileSelected ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-400 cursor-not-allowed'"
                        class="inline-flex items-center px-4 py-2 text-white font-semibold rounded-md transition duration-150 ease-in-out">
                    Guardar Documento
                </button>
            </div>
        </form>
    </div>
</x-modal>