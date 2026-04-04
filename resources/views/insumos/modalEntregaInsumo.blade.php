<x-modal name="modalEntregaInsumo-modal" maxWidth="6xl">
    <div class="p-6 space-y-6">
        <!-- Título -->
        <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
            Entrega de Insumo
        </h2>
        <form method="POST" id="formEntregaInsumo" action="{{ route('insumos.entregaInsumo') }}">
            @csrf
            <div class="flex flex-wrap border border-gray-200 rounded-lg">

                <div class="w-full max-w-full p-3 shrink-0 lg:w-6/12 2xl:w-4/12 md:flex-0">
                    <x-input-label for="id_insumo" :value="__('Seleccione Insumo *')" />
                    <x-select-input
                        name="id_insumo"
                        id="id_insumo"
                        :datax="true"
                        :options="$insumos"
                        :data="['id', 'nombre_insumo']"
                        class="block mt-1 w-full"
                        required
                    />
                </div>

                <div class="w-full max-w-full p-3 shrink-0 lg:w-6/12 2xl:w-4/12 md:flex-0">
                    <x-input-label for="cantidad" :value="__('Cantidad Entregada *')" />
                    <x-text-input id="cantidad" class="block mt-1 w-full bg-gray-200" type="number" name="cantidad" disabled required/>
                </div>

                <div class="w-full max-w-full p-3 shrink-0 lg:w-6/12 2xl:w-4/12 md:flex-0">
                    <x-input-label for="id_area_empresa" :value="__('Seleccione Area a quien se entrega *')" />
                    <x-select-input
                        name="id_area_empresa"
                        id="id_area_empresa"
                        :options="$areasEmpresa"
                        :data="['id', 'nombre_area']"
                        class="block mt-1 w-full"
                        required
                    />
                </div>

                <div class="flex w-full justify-between m-2">
                    <a  href="#" tabindex="0"
                        x-on:click="$dispatch('close-modal', 'modalEntregaInsumo-modal')"
                        class="bg-red-500 text-white px-4 py-2 rounded"
                    >
                        Cerrar
                    </a>
                    <x-primary-button class="ms-4">
                        Enviar
                    </x-primary-button>
                </div>
            </div>
        </form>

    </div>
</x-modal>
