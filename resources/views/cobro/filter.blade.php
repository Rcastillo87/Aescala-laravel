<div id="accordion-open" data-accordion="open">
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
             <form method="GET" action="{{route("material.index")}}">
                <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                <div class="flex flex-wrap gap-1">
                    <div class="p-2 shrink-0 w-[40]">
                        <x-input-label for="nombre_material" :value="__('Busqueda por Nombre Material')" />
                        <x-text-input id="nombre_material" class="block mt-1 w-full" type="text" name="nombre_material" :value="Request('nombre_material')"
                         autofocus />
                    </div>

                    <div class="p-2 shrink-0 w-[40]">
                        <x-input-label for="codigo" :value="__('Codigo Material')" />
                        <x-text-input id="codigo" class="block mt-1 w-full" type="text" name="codigo" :value="Request('codigo')"
                         autofocus />
                    </div>

                    @if (!Auth::user()->isAlmacenista || (Auth::user()->isAlmacenista && $tipoAlma == 3)))
                        <div class="p-2 shrink-0 w-[40]">
                            <x-input-label for="tipo" :value="__('Tipo Material')" />
                            <x-select-input
                                name="tipo"
                                :options="$tipos"
                                :selected="Request('tipo')"
                                class="block mt-1 w-full"
                            />
                        </div>
                    @endif

                    <div class="p-2 shrink-0 w-[40]">
                        <x-input-label for="estado" :value="__('Estado')" />
                        <x-select-input
                            name="estado"
                            :options="$estado"
                            :selected="Request('estado')"
                            class="block mt-1 w-full"
                        />
                    </div>
                    <div class="p-2 shrink-0 w-[40]">
                        <x-input-label for="rango" :value="__('Rango de cantidades')" />
                        <select name="rango" id="rango" class="block mt-1 w-full border-gray-300
                            focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option class="bg-gray-100" value="" @if(Request('rango') == '') selected @endif>-- Seleccione --</option>
                            <option class="bg-red-200" value="1" @if(Request('rango') == '1') selected @endif>Rojo</option>
                            <option class="bg-orange-200" value="2" @if(Request('rango') == '2') selected @endif>Naranja</option>
                            <option value="3" @if(Request('rango') == '3') selected @endif>Blanco</option>
                        </select>
                    </div>

                    <div class="p-2 shrink-0 w-[40]">
                        <x-input-label for="aprobar" :value="__('Aprobación para Despacho')" />
                        <select name="aprobar" id="aprobar" class="block mt-1 w-full border-gray-300
                            focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="" @if(Request('aprobar') == '') selected @endif>-- Seleccione --</option>
                            <option value="1" @if(Request('aprobar') == '1') selected @endif>Si</option>
                            <option value="0" @if(Request('aprobar') == '0') selected @endif>No</option>
                        </select>
                    </div>

                    <div class="p-2 shrink-0 w-[40]">
                        <x-input-label for="id_proveedor" :value="__('Proveedor')" />
                        <x-select-input
                            name="id_proveedor"
                            :data="['id', 'razon_social']"
                            :options="$proveedores"
                            :selected="Request('id_proveedor')"
                            class="block mt-1 w-full"
                        />
                    </div>

                    <div class="p-2 shrink-0 w-[40]">
                        <x-input-label for="zona" :value="__('Zona')" />
                        <x-select-input
                            name="zona"
                            :options="$zonas"
                            :selected="Request('zona')"
                            class="block mt-1 w-full"
                        />
                    </div>

                    <div class="p-2 shrink-0 w-[40]">
                        <x-input-label for="fase" :value="__('Fase')" />
                        <x-select-input
                            name="fase"
                            :options="$fases"
                            :selected="Request('fase')"
                            class="block mt-1 w-full"
                        />
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

                    <div class="p-2 shrink-0">
                        <button id="download-search-excel" class="inline-flex items-center px-3 py-2 text-sm font-medium
                             text-center rounded-lg text-[#242e68] bor-2  border-dolid border-2 border-[#242e68] hover:bg-[#242e68] hover:text-white mt-6"
                                data-tooltip-target="tooltip-search-download" data-tooltip-style="light">
                            <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13V4M7 14H5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1h-2m-1-5-4 5-4-5m9 8h.01"/>
                            </svg>
                        </button>
                        <div id="tooltip-search-download" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-gray-900
                            bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 tooltip ">
                            Descargar Busqueda en Excel
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
