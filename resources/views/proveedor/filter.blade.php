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
             <form method="GET" action="{{route("proveedor.index")}}">
                <div class="flex flex-wrap gap-1">
                    <div class="p-2 shrink-0 w-[40]">
                        <x-input-label for="razon_social" :value="__('Nombre | Razón')" />
                        <x-text-input id="razon_social" class="block mt-1 w-full" type="text" name="razon_social" :value="Request('razon_social')" 
                         autofocus />
                    </div>
                    <div class="p-2 shrink-0 w-[40]">
                        <x-input-label for="nit" :value="__('Documento | NIT')" />
                        <x-text-input id="nit" class="block mt-1 w-full" type="text" name="nit" :value="Request('nit')" />
                    </div>
                    <div class="p-2 shrink-0 w-[40]">
                        <x-input-label for="direccion" :value="__('Direccion')" />
                        <x-text-input id="direccion" class="block mt-1 w-full" type="text" name="direccion" :value="Request('direccion')" />
                    </div>
                    <div class="p-2 shrink-0 w-[40]">
                        <x-input-label for="telefono" :value="__('Telefono')" />
                        <x-text-input id="telefono" class="block mt-1 w-full" type="text" name="telefono" :value="Request('telefono')" />
                    </div>
                    <div class="p-2 shrink-0 w-[200px]">
                        <x-input-label for="activo" :value="__('activo')" />
                        <select name="activo" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 
                            dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                            <option value="">-- Seleccione --</option>
                            @foreach ($activo as $key => $value)
                                <option value="{{$key}}" {{(Request('activo') == $key)?'selected':'' }}>
                                    {{$value}}</option>
                            @endforeach
                        </select>
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