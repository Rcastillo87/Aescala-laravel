<div class="flex items-center justify-center gap-2">

    <!-- 1. BOTÓN VER NOVEDADES (Cambia estado de 1 a 2 automáticamente) -->
    <button type="button" 
        onclick="verNovedades({{ $item->id }}, {{ $item->estado }}, this)"
        data-novedades="{{ $item->novedades }}"
        class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-700 text-white border-2 border-blue-800 hover:bg-white hover:text-blue-800"
        title="Ver Novedades">
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M9 8h10M9 12h10M9 16h10M4.99 8H5m-.02 4h.01m0 4H5"/>
        </svg>
    </button>

    <!-- 2. BOTÓN AÑADIR COMENTARIO Y CAMBIAR ESTADO (Solo si estado es 1 o 2) -->
    @if(in_array($item->estado, [1, 2]))
    <button type="button" 
        onclick="abrirModalComentario({{ $item->id }})"
        class="flex items-center justify-center w-10 h-10 rounded-full bg-violet-700 text-white border-2 border-violet-800 hover:bg-white hover:text-violet-800"
        title="Añadir Comentario / Estado">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M8 20V7m0 13-4-4m4 4 4-4m4-12v13m0-13 4 4m-4-4-4 4"/>
        </svg>
    </button>
    @endif

    <!-- 3. BOTÓN VER COMENTARIO GUARDADO (Solo si existe comentario) -->
    @if(!empty($item->comentario) && Auth::user()->isAdmin)
        <button type="button"
            onclick="verComentario({{ Js::from($item->comentario) }}, {{ Js::from($estados[$item->estado] ?? '') }})"
            class="flex items-center justify-center w-10 h-10 rounded-full bg-amber-600 text-white border-2 border-amber-700 hover:bg-white hover:text-amber-800"
            title="Ver Comentario">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
            </svg>
        </button>
    @endif

    <!-- 4. BOTÓN DE ELIMINACIÓN (Solo si está en estado 1) -->
    @if($item->estado === 1)
    <button type="button" 
        onclick="eliminarNovedad({{ $item->id }})"
        class="flex items-center justify-center w-10 h-10 rounded-full bg-red-700 text-white border-2 border-red-800 hover:bg-white hover:text-red-800"
        title="Eliminar">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
    </button>
    @endif

</div>