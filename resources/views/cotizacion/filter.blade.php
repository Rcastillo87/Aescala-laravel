<div id="accordion-open" class="mb-4 shadow-md" data-accordion="open">
        <h2 id="accordion-open-heading-1" >
            <button type="button" class="flex items-center justify-between w-full p-2 font-medium rtl:text-right
             text-gray-500 border border-b-0 border-gray-200 rounded-t-xl dark:bg-white gap-3 "
                    data-accordion-target="#accordion-open-body-1" aria-expanded="false"
                    aria-controls="accordion-open-body-1">
                <span class="flex items-center text-[#242e68]">
                    <svg class="w-6 h-6 text-gray-800 dark:text-gray-500 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                      <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                            d="M18.796 4H5.204a1 1 0 0 0-.753 1.659l5.302 6.058a1 1 0 0 1 .247.659v4.874a.5.5 0 0 0 .2.4l3 2.25a.5.5 0 0 0 .8-.4v-7.124a1 1 0 0 1
                            .247-.659l5.302-6.059c.566-.646.106-1.658-.753-1.658Z"/>
                    </svg>
                Filtros
                </span>
                <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                </svg>
            </button>
        </h2>
        <div id="accordion-open-body-1" class="hidden" aria-labelledby="accordion-open-heading-1">
            <div class="p-1 border border-b-1 border-gray-200">
             <form method="GET" action="{{route("cotizacion.index")}}">
                <div class="flex flex-wrap gap-1">

                    <div class="p-2 shrink-0 w-[40]">
                        <x-input-label for="nomProyecto" :value="__('Nombre del Proyecto')" />
                        <x-text-input id="nomProyecto" class="block mt-1 w-full" type="text" name="nomProyecto" :value="Request('nomProyecto')" 
                         autofocus />
                    </div>

                    <div class="relative p-2 shrink-0 w-[40]">
                        <x-input-label for="fecha" :value="__('Fecha de Cotizacion')" />
                        <div class="relative max-w-sm">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                </svg>
                            </div>
                            <input 
                                datepicker=""
                                datepicker-format="yyyy-mm-dd"
                                autocomplete="off"
                                type="text" 
                                id="fecha" 
                                name="fecha" 
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 ps-10 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" 
                                value="{{ Request('fecha') }}"
                            />
                        </div>
                    </div>
                    <div class="p-2 shrink-0">
                        <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium
                             text-center rounded-lg text-[#242e68] bor-2  border-dolid border-2 border-[#242e68] hover:bg-[#242e68] hover:text-white mt-6"
                                data-tooltip-target="tooltip-search" data-tooltip-style="light">
                            <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/>
                            </svg>
                        </button>
                        <div id="tooltip-search" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-gray-900 
                            bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 tooltip ">
                            Buscar
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>