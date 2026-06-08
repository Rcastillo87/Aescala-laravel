<div class="flex items-center justify-center space-x-2">

    @if ($item->estado == 1)
        <div class="relative inline-flex">
            <button onclick="abrirAcuerdoPago({{ $item->id }}, '{{ $item->proyecto->nombre_proyecto ?? 'Proyecto' }}')"
                data-tooltip-target="tooltip-fecha-acuerdo-{{ $item->id }}" data-tooltip-trigger="hover"
                class="flex items-center justify-center w-10 h-10 text-white bg-blue-700 hover:bg-white hover:text-blue-800 border-2 border-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16 10 3-3m0 0-3-3m3 3H5v3m3 4-3 3m0 0 3 3m-3-3h14v-3"/>
                </svg>
            </button>
            <div id="tooltip-fecha-acuerdo-{{ $item->id }}"
                    role="tooltip"
                    class="absolute z-50 invisible opacity-0 inline-block px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm tooltip">
                Fecha Acuerdo de Pago
            </div>
        </div>

        <div class="relative inline-flex">
            <button type="button" data-tooltip-target="tooltip-notificacion-{{ $item->id }}" data-tooltip-trigger="hover"
                onclick="openNotificacionModal(
                    {{ $item->id }}, 
                    '{{ addslashes($item->proyecto->nombre_cliente) }}', 
                    '{{ $item->proyecto->telefono_cliente }}', 
                    '{{ addslashes($item->proyecto->nombre_proyecto) }}', 
                    {{ $item->valor_pendiente }}
                )"
                class="flex items-center justify-center w-10 h-10 text-white bg-orange-600 hover:bg-orange-700 rounded-full transition-colors">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16v-5.5A3.5 3.5 0 0 0 7.5 7m3.5 9H4v-5.5A3.5 3.5 0 0 1 7.5 7m3.5 9v4M7.5 7H14m0 0V4h2.5M14 7v3m-3.5 6H20v-6a3 3 0 0 0-3-3m-2 9v4m-8-6.5h1"/>
                </svg>
            </button>
            <div id="tooltip-notificacion-{{ $item->id }}"
                    role="tooltip"
                    class="absolute z-50 invisible opacity-0 inline-block px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm tooltip">
                Notificación por whatsapp
            </div>
        </div>

        <div class="relative inline-flex">
            <button data-tooltip-target="tooltip-delete-{{ $item->id }}" data-tooltip-trigger="hover" onclick="deleteCobro({{ $item->id }})" 
                class="flex items-center justify-center w-10 h-10 text-white bg-red-700 hover:bg-white hover:text-red-800 border-2 border-red-800 focus:ring-4
                    focus:outline-none focus:ring-red-300 font-medium rounded-full text-sm dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"></path>
                </svg>
            </button>
            <div id="tooltip-delete-{{ $item->id }}"
                    role="tooltip"
                    class="absolute z-50 invisible opacity-0
                        inline-block px-3 py-2 text-sm font-medium
                        text-white bg-gray-900 rounded-lg shadow-sm tooltip">
                Eliminar
            </div>
        </div>
    @endif
</div>
