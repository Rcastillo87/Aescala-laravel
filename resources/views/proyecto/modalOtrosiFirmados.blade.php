<x-modal name="otrosiFirmados-modal" :show="false" maxWidth="md">
    <div class="p-6">
        <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800" id="otrosiModalTitle">
                Otrosí Firmados
            </h2>
            <button type="button"
                    @click="$dispatch('close-modal', 'otrosiFirmados-modal')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18 17.94 6M18 18 6.06 6"/>
                </svg>
            </button>
        </div>

        <div id="otrosiModalList" class="space-y-2 max-h-[70vh] overflow-y-auto">
            {{-- Se llena por JS --}}
        </div>
    </div>
</x-modal>