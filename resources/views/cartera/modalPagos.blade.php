<x-modal name="modalPagos-modal" maxWidth="4xl">
    <div class="p-6" x-data>
        <h2 id="txTitulo" class="text-2xl font-bold mb-6 text-gray-800 text-center"></h2>

        <!-- Tabla responsive -->
        <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-md bg-white">
            <table class="min-w-full text-sm text-gray-700">
                <thead class="bg-green-700 text-white text-center uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Descripción</th>
                        <th class="px-4 py-3">%</th>
                        <th class="px-4 py-3 text-right">Valor a Pagar</th>
                        <th class="px-4 py-3 text-right">Valor Pagado</th>
                        <th class="px-4 py-3">Fecha de Pago</th>
                        <th class="px-4 py-3">Estado del Pago</th>
                    </tr>
                </thead>
                <tbody id="tablaPagos" class="divide-y divide-gray-100 text-center">
                    <!-- Se llena dinámicamente -->
                    
                </tbody>
            </table>
        </div>

        <!-- Botones inferiores -->
        <div class="flex justify-end mt-8 space-x-3">
            <button type="button"
                    x-on:click="$dispatch('close-modal', 'modalPagos-modal')"
                    class="bg-red-500 text-white px-5 py-2.5 rounded-lg shadow hover:bg-red-600 focus:ring-4 focus:ring-red-300 transition-all">
                Cerrar
            </button>
        </div>
    </div>
</x-modal>
