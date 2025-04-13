<!-- Modal -->
<x-modal name="balance-modal" maxWidth="6xl">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-4">Balance General</h2>
        <div id="listaBalance"  class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2"></div>
        <div class="flex justify-start">
            <a tabindex="0"
                x-on:click="$dispatch('close-modal', 'balance-modal')"
                class="bg-red-500 text-white px-4 py-2 rounded"
            >
                Cerrar
            </a>
        </div>
    </div>
</x-modal>