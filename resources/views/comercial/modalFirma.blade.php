<x-modal name="firma-modal" maxWidth="2xl" :closeOnOutsideClick="false" :closeOnEscape="false">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">Añade tu Firma</h2>

        <div class="w-full">
            <!-- Contenedor del canvas -->
            <div id="signature-pad-container"
                 class="border border-gray-300 rounded-lg bg-gray-50 shadow-inner relative h-56 w-full overflow-hidden">
                <canvas id="signature-pad" class="absolute top-0 left-0 w-full h-full"></canvas>
            </div>

            <!-- Botón borrar -->
            <div class="flex justify-start mt-3">
                <button type="button" id="clear-signature"
                        class="flex items-center bg-gray-500 text-white px-3 py-1 rounded-lg shadow hover:bg-gray-600 transition">
                    Borrar Firma
                </button>
            </div>

            <!-- Botones -->
            <div class="flex justify-between mt-6">
                <button type="button"
                        x-on:click="$dispatch('close-modal', 'firma-modal')"
                        class="bg-red-500 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600 transition">
                    Cerrar
                </button>

                <button type="button" id="save-signature"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                    Guardar Firma
                </button>
            </div>
        </div>
    </div>
</x-modal>
