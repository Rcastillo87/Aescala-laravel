<!-- Modal -->
<x-modal name="factura-modal" maxWidth="4xl" :closeOnOutsideClick="false" :closeOnEscape="false">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-4">Factura</h2>
        <div class="p-0 overflow-x-auto ps ps--active-x">
            <table class="items-center w-full mb-0 align-top border-collapse dark:border-white/40 text-slate-500">
                <x-table-header :headers="$headerFactura" />
                <tbody id="facturaList"></tbody>
            </table>
            <!-- Paginador -->
            <div id="paginationFactura" class="flex justify-center mt-4">
                <!-- Aquí se cargará la paginación -->
            </div>
            <div id="noDataMessageFactura" class="hidden text-center text-gray-500">
                No hay datos disponibles.
            </div>
        </div>
        <div class="flex justify-star">
            <a  href="#" tabindex="0"
                x-data
                x-on:click="$dispatch('close-modal', 'factura-modal')"
                class="bg-red-500 text-white px-4 py-2 rounded"
            >
                Cerrar
            </a>
        </div>
    </div>
</x-modal>