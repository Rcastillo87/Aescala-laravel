<!-- Modal -->
<x-modal name="despachos-modal" maxWidth="6xl" :closeOnOutsideClick="false" :closeOnEscape="false">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-4">Despachos</h2>
        <div id="listaDespachos" class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2"></div>
        <div id="totalFacturado" class="flex w-full max-w-full my-2 justify-center"></div>
        <div class="flex justify-between">
            <a href="#" tabindex="0"
                x-on:click="$dispatch('close-modal', 'despachos-modal')"
                class="bg-red-500 text-white px-4 py-2 rounded"
            >
                Cerrar
            </a>
            <x-secondary-button id="botonDescarga" class="hidden text-xl cursor-pointer" href="#">
                <svg class="mr-2 w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M4 15v2a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-2m-8 1V4m0 12-4-4m4 4 4-4"/>
                </svg>
                Factura Despachos
            </x-secondary-button>
        </div>
    </div>
</x-modal>