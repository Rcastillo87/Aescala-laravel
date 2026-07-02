<x-modal name="modalOtroSiRefe" maxWidth="lg" :closeOnOutsideClick="false" :closeOnEscape="false">
    <div class="p-6" x-data>

        <h2 id="txTitlePagoRefe" class="text-2xl font-bold mb-4 text-gray-800 text-center">Crear Referecia de Pago </h2>

        <form id="formOtrosiRefe" method="POST" action="{{ route('otro_si.saveRefe') }}">
            @csrf
            <div class="flex flex-wrap border border-gray-200 m-2 rounded-lg">
                <input type="hidden" name="id_proyecto" value="{{$id_proyecto}}">
                <input type="hidden" name="numero" id="numero">
                <input type="hidden" name="referencia" id="referencia">

                <div class="max-w-full p-2 shrink-0 w-full md:flex-0">
                    <x-input-label for="valor" :value="__('Valor de Referencia *')" />
                    <x-text-input  min="1" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-300 focus:border-blue-400 mt-1 moneda-cop"
                        type="number" name="valor_referecia" id="valor_referecia" placeholder="Ej: 200000" required/>
                        <p id="txlabelValor"></p>
                </div>

                <!-- Botones -->
                <div class="flex w-full justify-end p-2">
                    <button form="formOtrosiRefe" type="submit"
                        class="bg-green-600 text-white px-6 py-2.5 rounded-lg shadow hover:bg-green-700 focus:ring-4 focus:ring-green-300 transition-all">
                        Guardar Refencia
                    </button>
                </div>
            </div>
        </form>
        <!-- Botones -->
        <div class="flex justify-end pt-2">
            <button type="button"
                    x-on:click="$dispatch('close-modal', 'modalOtroSiRefe')"
                    class="bg-red-500 text-white px-5 py-2.5 rounded-lg shadow hover:bg-red-600 focus:ring-4 focus:ring-red-300 transition-all">
                Cerrar
            </button>
        </div>

    </div>
</x-modal>
