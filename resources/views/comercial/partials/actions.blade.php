<div class="flex items-center justify-center gap-2">

    @php
        $btnBase = 'flex items-center justify-center w-10 h-10 rounded-full border-2 
                    focus:outline-none focus-visible:ring-4 font-medium text-sm
                    transition-colors duration-200';
    @endphp

    <!-- Botón Editar -->
    <div class="relative inline-flex">
        <a
            href="{{ route('comercial.edit', $item->id) }}"
            tabindex="0"
            aria-label="Editar proyecto"
            data-tooltip-target="tooltip-edit-{{ $item->id }}"
            data-tooltip-trigger="hover"
            class="{{ $btnBase }} 
                   bg-green-700 text-white border-green-800
                   hover:bg-white hover:text-green-800
                   focus-visible:ring-green-300
                   dark:bg-green-600 dark:hover:bg-green-700 dark:focus-visible:ring-green-800"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10
                        a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91
                        a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14
                        l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
            </svg>
        </a>

        <div id="tooltip-edit-{{ $item->id }}" role="tooltip"
            class="absolute z-10 invisible opacity-0 px-3 py-2 text-sm font-medium text-gray-900 bg-white border-2 rounded-lg shadow-xs tooltip dark:bg-gray-700">
            Editar
            <div class="tooltip-arrow" data-popper-arrow></div>
        </div>
    </div>

    <!-- Botón Link Firma -->
    <div class="relative inline-flex" x-data>
        <button
            type="button"
            tabindex="0"
            aria-label="Generar link de firma"
            data-tooltip-target="tooltip-link-{{ $item->id }}"
            data-tooltip-trigger="hover"
            data-id="{{ $item->id }}" 
            data-link="{{ $item->tokenEncrip }}" 
            x-on:click="$dispatch('open-modal', 'sendLink-modal')" 
            x-data="" 
            onclick="setModalData(this)"
            class="{{ $btnBase }}
                   bg-blue-700 text-white border-blue-800
                   hover:bg-white hover:text-blue-800
                   focus-visible:ring-blue-300
                   dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus-visible:ring-blue-800"
        >
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13.213 9.787a3.391 3.391 0 0 0-4.795 0l-3.425 3.426a3.39 3.39 0 0 0 4.795 4.794l.321-.304m-.321-4.49a3.39 3.39 0 0 0 4.795 0l3.424-3.426a3.39 3.39 0 0 0-4.794-4.795l-1.028.961"/>
            </svg>
        </button>

        <div id="tooltip-link-{{ $item->id }}" role="tooltip"
            class="absolute z-10 invisible opacity-0 px-3 py-2 text-sm font-medium text-gray-900 bg-white border-2 rounded-lg shadow-xs tooltip dark:bg-gray-700">
            Link Firma
            <div class="tooltip-arrow" data-popper-arrow></div>
        </div>
    </div>

</div>
