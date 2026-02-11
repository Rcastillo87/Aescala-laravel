<div class="flex items-center justify-center space-x-2">

    <!-- Cambiar estado -->
    <div class="relative inline-flex">
        <button
            onclick="cambiarEstado({{ $item->id }}, {{ $item->activo }})"
            data-tooltip-target="tooltip-state-{{ $item->id }}"
            data-tooltip-trigger="hover"
            class="flex items-center justify-center w-10 h-10
                   text-white bg-violet-700
                   hover:bg-white hover:text-violet-800
                   border-2 border-violet-800
                   focus:ring-4 focus:ring-violet-300
                   rounded-full"
        >
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-width="2"
                      d="M8 20V7m0 13-4-4m4 4 4-4m4-12v13m0-13 4 4m-4-4-4 4"/>
            </svg>
        </button>

        <div id="tooltip-state-{{ $item->id }}"
             role="tooltip"
             class="absolute z-50 invisible opacity-0
                    inline-block px-3 py-2 text-sm font-medium
                    text-white bg-gray-900 rounded-lg shadow-sm tooltip">
            Cambio de estado
        </div>
    </div>

    <!-- Editar -->
    <div class="relative inline-flex">
        <a href="{{ route('material.edit', $item->id) }}"
           data-tooltip-target="tooltip-edit-{{ $item->id }}"
           data-tooltip-trigger="hover"
           class="flex items-center justify-center w-10 h-10
                  text-white bg-green-700
                  hover:bg-white hover:text-green-800
                  border-2 border-green-800
                  focus:ring-4 focus:ring-green-300
                  rounded-full">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10
                        a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91
                        a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14
                        l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
            </svg>
        </a>

        <div id="tooltip-edit-{{ $item->id }}"
             role="tooltip"
             class="absolute z-50 invisible opacity-0
                    inline-block px-3 py-2 text-sm font-medium
                    text-white bg-gray-900 rounded-lg shadow-sm tooltip">
            Editar
        </div>
    </div>

</div>
