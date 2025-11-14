<x-modal name="modalPagos-modal" maxWidth="4xl">
    <div class="p-6" x-data>
        <h2 id="txTitulo" class="text-2xl font-bold mb-6 text-gray-800 text-center"></h2>

        <div class="flex justify-end my-2">
            <x-primary-button id="btnModalFormpagoForm">
                Añadir Pago
            </x-primary-button>
        </div>

        <!-- Tabla responsive -->
        <div id="tablaPagos" class="overflow-x-auto rounded-xl border border-gray-200 shadow-md bg-white">

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
