<x-modal name="notas-modal" maxWidth="4xl">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-4">Notas Proyecto</h2>

        <div class="flex items-start sm:items-center p-4 mb-4 text-sm text-blue-700 rounded-lg bg-blue-50 border border-blue-200" role="alert">
            <svg class="w-4 h-4 me-2 shrink-0 mt-0.5 sm:mt-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
            <p>
                <span class="font-medium me-1">⚠️ Importante:</span>
                Los contratos ya cuentan con notas por defecto. Puedes modificarlas o añadir nuevas fácilmente desde esta interfaz.
            </p>
        </div>

        <div class="w-full px-2">
            <form class="flex flex-wrap -mx-3" id="formNotas">
                <div class="flex w-full items-center">
                    <div class="p-3 shrink-0">
                        <x-secondary-button class="py-1 px-1" id="addItem-nota" href="#" data-tooltip-target="tooltip-hover-item">
                            <svg class="w-6 h-6 text-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
                            </svg>
                        </x-secondary-button>
                        <div id="tooltip-hover-item" role="tooltip"
                            class="absolute z-10 invisible px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                            Añadir nota
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </div>

                    <div id="notas-items" class="w-full">
                        {{-- Las notas se renderizan dinámicamente por JS --}}
                    </div>
                </div>

                <div class="flex w-full justify-between mt-4">
                    <a href="#" tabindex="0"
                        x-on:click="$dispatch('close-modal', 'notas-modal')"
                        class="bg-red-500 text-white px-4 py-2 rounded"
                        id="cerrar-notas-modal">
                        Cerrar
                    </a>
                    <x-primary-button class="ms-4" id="guardar-notas">
                        Guardar
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-modal>
