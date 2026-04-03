<div class="flex items-center justify-center gap-2">

    <a
        onclick="cambiarEstado({{ $item->id }}, {{ $item->estado }})"
        class="flex items-center justify-center w-10 h-10 rounded-full
               bg-violet-700 text-white border-2 border-violet-800
               hover:bg-white hover:text-violet-800
               cursor-pointer">

        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  d="M8 20V7m0 13-4-4m4 4 4-4m4-12v13m0-13 4 4m-4-4-4 4"/>
        </svg>
    </a>

    <a
        href="{{ route('insumos.edit', $item->id) }}"
        class="flex items-center justify-center w-10 h-10 rounded-full
               bg-green-700 text-white border-2 border-green-800
               hover:bg-white hover:text-green-800">

        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10
                     a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91
                     a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14
                     l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
        </svg>
    </a>

</div>
