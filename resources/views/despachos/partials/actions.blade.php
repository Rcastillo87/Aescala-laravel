<div class="flex items-center justify-center gap-2">

    <a tabindex="0" href="pdfDespacho?codigo={{ $item->codigo }}&id={{ $item->id_proyecto}}&view=pdf" target="_blank"
        class="tooltip flex items-center justify-center w-10 h-10 text-white bg-fuchsia-600 hover:bg-white hover:text-fuchsia-500 border-2 border-fuchsia-500 focus:ring-4 
            focus:outline-none focus:ring-fuchsia-300 font-medium rounded-full text-sm dark:bg-fuchsia-400 dark:hover:bg-fuchsia-500 dark:focus:ring-fuchsia-500 cursor-pointer">
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 15v2a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-2m-8 1V4m0 12-4-4m4 4 4-4"></path>
        </svg>
        <span class="tooltiptext">PDf del Despacho</span>
    </a>
</div>
