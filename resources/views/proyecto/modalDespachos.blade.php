<!-- Modal -->
<x-modal name="despachos-modal" maxWidth="6xl">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-4">Despachos</h2>
        <div id="listaDespachos" class=""></div>
        <div class="flex justify-start">
            <a tabindex="0"
                x-on:click="$dispatch('close-modal', 'despachos-modal')"
                class="bg-red-500 text-white px-4 py-2 rounded"
            >
                Cerrar
            </a>
        </div>
    </div>
</x-modal>