<x-modal name="modal-soporte" focusable maxWidth="3xl" :closeOnOutsideClick="false" :closeOnEscape="false">
    <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-2xl" x-data="{
        pagoId: null,
        tieneArchivo: false,
        previewUrl: null,
        nombreDoc: '',
        fileSelected: false,
        isPdf: false,
        init() {
            window.addEventListener('documentoModal', (e) => {
                this.pagoId = e.detail.id_pago;
                this.tieneArchivo = e.detail.tiene_archivo;
                this.previewUrl = e.detail.url_ver;
                this.nombreDoc = e.detail.nombre_archivo;
                this.isPdf = this.nombreDoc?.toLowerCase().endsWith('.pdf');
                this.fileSelected = false;
                $dispatch('open-modal', 'modal-soporte');
            });
        },
        handleFile(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Tipos permitidos
            const tiposPermitidos = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
            const tamanoMaximo = 10 * 1024 * 1024; // 10MB

            // Validar Tipo
            if (!tiposPermitidos.includes(file.type)) {
                alert('Formato no permitido. Por favor sube PDF o imágenes (JPG, PNG).');
                e.target.value = ''; // Limpiar el input
                this.fileSelected = false;
                return;
            }

            // Validar Tamaño
            if (file.size > tamanoMaximo) {
                alert('El archivo es muy pesado. Máximo 10MB.');
                e.target.value = '';
                this.fileSelected = false;
                return;
            }

            // Si pasa las pruebas, mostramos la previsualización
            this.fileSelected = true;
            this.isPdf = file.type === 'application/pdf';
            this.previewUrl = URL.createObjectURL(file);
        }
    }">
        <!-- Cabecera Estilizada -->
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/20 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-extrabold text-gray-800 dark:text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Soporte de Pago <span class="text-indigo-600 dark:text-indigo-400" x-text="'#' + pagoId"></span>
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Gestione el documento de respaldo para este registro.</p>
            </div>
            <template x-if="tieneArchivo">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                    <span class="w-2 h-2 mr-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Documento Activo
                </span>
            </template>
        </div>

        <form action="{{ route('documento.save') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <input type="hidden" name="id_tabla" id="id_tabla">
            <input type="hidden" name="id_registro" :value="pagoId">

            <!-- Contenedor Principal de Previsualización -->
            <div class="space-y-2">

                <!-- Sección: Archivo Actual -->
                <template x-if="tieneArchivo && !fileSelected">
                    <div class="relative group">
                        <div class="absolute -top-3 left-4 px-2 bg-white dark:bg-gray-800 text-xs font-semibold text-gray-400 uppercase tracking-wider z-10">Archivo Actual</div>
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden bg-gray-50 dark:bg-gray-900/50 shadow-inner">
                            <div class="p-3 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-white dark:bg-gray-800">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-300 truncate max-w-xs" x-text="nombreDoc"></span>
                                <a :href="previewUrl" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-700 font-bold flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    Abrir Original
                                </a>
                            </div>
                            <div class="flex justify-center p-4 min-h-[200px]">
                                <template x-if="isPdf">
                                    <iframe :src="previewUrl" class="w-full h-[400px] rounded-lg shadow-sm" frameborder="0"></iframe>
                                </template>
                                <template x-if="!isPdf">
                                    <img :src="previewUrl" class="max-h-[400px] object-contain rounded-lg shadow-md transition-transform duration-300 hover:scale-[1.02]">
                                </template>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Sección: Carga de Archivo (Estilo Dropzone) -->
                <div class="relative">
                    <label class="group flex flex-col items-center justify-center w-full h-25 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-2xl cursor-pointer bg-gray-50 dark:bg-gray-900/20 hover:bg-indigo-50 dark:hover:bg-indigo-900/10 hover:border-indigo-400 transition-all duration-300">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <div class="p-3 bg-white dark:bg-gray-800 rounded-full shadow-sm mb-1 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            </div>
                            <p class="mb-1 text-sm text-gray-700 dark:text-gray-300"><span class="font-bold">Haga clic para subir</span> o arrastre aquí</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">PDF, PNG, JPG (Máx. 1MB)</p>
                        </div>
                        <input
                            type="file"
                            name="archivo"
                            class="hidden"
                            @change="handleFile"
                            accept=".pdf, image/jpeg, image/png, image/jpg"
                            required
                        />
                    </label>
                </div>

                <!-- Previsualización Nueva (Solo si se selecciona archivo) -->
                <template x-if="fileSelected">
                    <div class="animate-fadeIn mt-4 p-4 border border-indigo-100 dark:border-indigo-900/50 bg-indigo-50/30 dark:bg-indigo-900/10 rounded-2xl">
                        <div class="flex items-center gap-2 mb-3 text-indigo-700 dark:text-indigo-400 font-bold text-sm">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                            Nueva selección lista para guardar
                        </div>
                        <div class="rounded-xl overflow-hidden shadow-lg bg-white dark:bg-gray-900 border border-indigo-200 dark:border-indigo-800">
                            <template x-if="isPdf">
                                <iframe :src="previewUrl" class="w-full h-80" frameborder="0"></iframe>
                            </template>
                            <template x-if="!isPdf">
                                <img :src="previewUrl" class="max-h-80 mx-auto object-contain">
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Footer con Botones -->
            <div class="mt-4 flex items-center justify-end space-x-4 border-t border-gray-100 dark:border-gray-700 pt-6">
                <button type="button" x-on:click="$dispatch('close')"
                        class="px-5 py-2.5 text-sm rounded-xl font-semibold text-white bg-red-500 hover:bg-red-700 transition-colors">
                    Cancelar
                </button>

                <button type="submit"
                        x-show="fileSelected"
                        class="inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200 dark:shadow-none hover:-translate-y-0.5 transition-all active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Actualizar Soporte
                </button>
            </div>
        </form>
    </div>
</x-modal>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn 0.4s ease-out forwards; }
</style>
