<!-- Modal -->
<x-modal name="modalHistoryInsumo-modal" maxWidth="4xl">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-4">Lista Historial de Insumo Entregado</h2>
        <div class="p-0 overflow-x-auto ps ps--active-x">
            <table class="items-center w-full mb-0 align-top border-collapse dark:border-white/40 text-slate-500">
                <x-table-header :headers="$headerModal" />
                <tbody id="serviceList"></tbody>
            </table>
            <!-- Paginador -->
            <div id="pagination" class="flex justify-center mt-4">
                <!-- Aquí se cargará la paginación -->
            </div>
            <div id="noDataMessage" class="hidden text-center text-gray-500">
                No hay datos disponibles.
            </div>
        </div>
        <div class="flex justify-star">
            <button
                x-data
                x-on:click="$dispatch('close-modal', 'modalHistoryInsumo-modal')"
                class="bg-red-500 text-white px-4 py-2 rounded"
            >
                Cerrar
            </button>
        </div>
    </div>
</x-modal>
