<x-modal name="sendLink-modal" maxWidth="xl">
    <div class="p-6">
        @php
            $text = Request::is('*otro_si*')? 'de Firmar Otrosí' : 'Firmar de Contrato';
        @endphp
        <h2 class="text-xl font-semibold mb-4 text-gray-700">Enviar Link {{$text}}</h2>

        <!-- Input oculto con el link -->
        <input type="hidden" id="sendLink" value="">
        <input type="hidden" id="id_proyect_link" value="">

        <!-- Input visible para correo -->
        <div class="mb-4">
            <label for="emailDestino" class="block text-sm font-medium text-gray-700 mb-1">Ingrese un Correo</label>
            <input type="email" id="emailDestino" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200">
        </div>

        <div class="w-full">
            <!-- Botones -->
            <div class="flex justify-between mt-6 space-x-2">
                <!-- Cerrar -->
                <button type="button"
                        x-on:click="$dispatch('close-modal', 'sendLink-modal')"
                        class="bg-red-500 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600 transition">
                    Cerrar
                </button>

                <div class="space-x-2">
                    <!-- Copiar -->
                    <button type="button"
                            onclick="copyLink()"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 transition">
                        Copiar link
                    </button>

                    <!-- Enviar correo -->
                    <button type="button"
                            onclick="sendLinkByEmail()"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                        Enviar por correo
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-modal>
