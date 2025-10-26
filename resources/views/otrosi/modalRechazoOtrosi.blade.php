<x-modal name="modalRechazoOtrosi-modal" maxWidth="2xl">
    <div class="p-6 space-y-6">
        @php
            $hidden = Request::is('*otro_si*')? 'hidden' : '';
            $disabled = Request::is('*otro_si*')? 'disabled' : '';
            $gray = Request::is('*otro_si*')? 'bg-gray-50' : '';
        @endphp
        <!-- Título -->
        <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M5.93 19h12.14a2 2 0 001.8-2.9l-6.07-10.5a2 2 0 00-3.46 0L4.13 16.1A2 2 0 005.93 19z" />
            </svg>
            Motivo de Rechazo del Otro Sí
        </h2>

        <!-- Descripción -->
        <p class="text-gray-600 text-sm {{ $hidden }}">
            Si no acepta los términos del otro sí, por favor indique brevemente los motivos de su decisión.
        </p>

        <!-- Campo de texto -->
        <div>
            <x-input-label for="sugerencia_cliente" :value="__('Ingrese comentario *')" class="{{ $hidden }}"/>
            <textarea 
                id="sugerencia_cliente" 
                name="sugerencia_cliente" 
                rows="4"
                class="block mt-2 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 {{ $gray }}"
                placeholder="Describa las razones del rechazo..."
                autofocus {{ $disabled }}
            ></textarea>
            <x-input-error :messages="$errors->get('sugerencia_cliente')" class="mt-2" />
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-4 pt-4 border-t border-gray-200 {{ $hidden }}">
            <button type="button"
                    x-on:click="$dispatch('close-modal', 'modalRechazoOtrosi-modal')"
                    class="px-5 py-2.5 rounded-lg bg-gray-200 text-gray-700 font-medium hover:bg-gray-300 transition">
                Cancelar
            </button>

            <button type="button" id="rechazo-otro-si"
                    class="px-5 py-2.5 rounded-lg bg-red-600 text-white font-medium hover:bg-red-700 transition">
                Enviar Rechazo
            </button>
        </div>
    </div>
</x-modal>
