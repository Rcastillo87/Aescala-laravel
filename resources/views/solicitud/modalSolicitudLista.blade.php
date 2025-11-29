<x-modal name="modalSolicitudLista-modal" maxWidth="4xl">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">Lista items solicitados</h2>

        <div class="w-full">
            <!-- TABLA DE ITEMS -->
            <table class="w-full text-left border border-gray-300 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-3 py-2 border">Material Solicitado</th>
                        <th class="px-3 py-2 border">Cantidad Solicitado</th>
                        <th class="px-3 py-2 border">Estado</th>
                        <th class="px-3 py-2 border">Cantidad Despachada</th>
                        <th class="px-3 py-2 border">Fecha del Despacho</th>
                        <th class="px-3 py-2 border">Código del Despacho</th>
                        <th class="px-3 py-2 border">Opciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyListaItems">
                    <!-- Se llena por JS -->
                </tbody>
            </table>

            <!-- Botones -->
            <div class="flex justify-start mt-6">
                <button type="button"
                        x-on:click="$dispatch('close-modal', 'modalSolicitudLista-modal')"
                        class="bg-red-500 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600 transition">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</x-modal>
