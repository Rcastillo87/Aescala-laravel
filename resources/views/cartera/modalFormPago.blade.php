<x-modal name="modalFormPago-modal" maxWidth="3xl">
    <div class="p-6 bg-white rounded-2xl shadow-xl">
        <h2 id="tituloFormPago" class="text-2xl font-bold mb-3 text-center text-gray-800"></h2>
        <h3 id="tituloDescripccion" class="text-md mb-6 text-center text-gray-600"></h3>

        <form id="formPago" method="POST" action="{{ route('cartera.save') }}" class="space-y-5">
            @csrf
            <input type="hidden" id="proyecto_id" name="proyecto_id">
            <input type="hidden" id="campo_desc" name="campo_desc">
            <input type="hidden" id="tipo" name="tipo">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="valor_pago" :value="__('Valor a pagar')" />
                    <div class="mt-1 text-xl font-semibold text-blue-600" id="valorApagarLabel">$0</div>
                </div>

                <div>
                    <x-input-label for="valor_pagado" :value="__('Valor Pagado *')" />
                    <x-text-input id="valor_pagado" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-300 focus:border-blue-400 moneda-cop"
                        type="number" name="valor_pagado" value="{{ old('valor_pagado') }}" placeholder="Ej: 200000" />
                    <x-input-error :messages="$errors->get('valor_pagado')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="comentario" :value="__('Comentario')" />
                <textarea id="comentario" name="comentario"
                    class="block w-full mt-1 h-28 rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                    placeholder="Agrega una observación si lo deseas..."></textarea>
                <x-input-error :messages="$errors->get('comentario')" class="mt-2" />
            </div>

            <!-- Botones -->
            <div class="flex justify-end pt-4 space-x-3 border-t">
                <button type="button"
                        x-on:click="$dispatch('close-modal', 'modalFormPago-modal')"
                        class="bg-red-500 text-white px-5 py-2.5 rounded-lg shadow hover:bg-red-600 focus:ring-4 focus:ring-red-300 transition-all">
                    Cerrar
                </button>

                <button form="formPago" type="submit"
                    class="bg-green-600 text-white px-6 py-2.5 rounded-lg shadow hover:bg-green-700 focus:ring-4 focus:ring-green-300 transition-all">
                    Guardar Pago
                </button>
            </div>
        </form>
    </div>
</x-modal>
