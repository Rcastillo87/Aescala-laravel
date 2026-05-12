<x-modal name="modalRC" maxWidth="md">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">

        <!-- Header -->
        <div class="px-4 py-2 border-b border-gray-100 bg-gray-50">
            <h2 class="text-lg md:text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 6.2V5h11v1.2M8 5v14m-3 0h6m2-6.8V11h8v1.2M17 11v8m-1.5 0h3"/>
                </svg>
                Añadir RC
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Ingrese el número de RC correspondiente al pago.
            </p>
        </div>

        <!-- Body -->
        <form action="{{ route('cartera.sendRC') }}" method="POST" class="px-4 py-2">
            @csrf

            <input type="hidden" id="id_pago_rc" name="id_pago_rc">

            <!-- Input -->
            <div class="w-full">
                <x-input-label
                    for="rc"
                    :value="__('Número RC *')"
                    class="mb-2"
                />

                <x-text-input
                    id="rc"
                    class="block w-full rounded-xl border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500"
                    type="text"
                    name="rc"
                    placeholder="Ingrese el RC"
                    required
                    autofocus
                />
            </div>

            <!-- Footer -->
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-2">

                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', 'modalRC')"
                    class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 rounded-xl bg-red-500 text-white font-medium shadow-sm hover:bg-red-600 transition"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 rounded-xl bg-orange-600 text-white font-semibold shadow-sm hover:bg-orange-700 transition"
                >
                    Guardar RC
                </button>

            </div>
        </form>
    </div>
</x-modal>
