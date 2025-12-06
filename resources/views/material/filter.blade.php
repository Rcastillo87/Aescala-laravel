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
             <form method="GET" action="{{route("material.index")}}">
                <div class="flex flex-wrap gap-1">
                    <div class="p-2 shrink-0 w-[40]">
                        <x-input-label for="nombre_material" :value="__('Nombre Material')" />
                        <x-text-input id="nombre_material" class="block mt-1 w-full" type="text" name="nombre_material" :value="Request('nombre_material')" 
                         autofocus />
                    </div>
                    <div class="p-2 shrink-0 w-[40]">
                        <x-input-label for="cantidad" :value="__('Cantidad')" />
                        <x-text-input id="cantidad" class="block mt-1 w-full" type="number" name="cantidad" :value="Request('cantidad')" 
                         autofocus />
                    </div>
                    <div class="p-2 shrink-0 w-[40]">
                        <x-input-label for="cantidad_min" :value="__('Cantidad Minima')" />
                        <x-text-input id="cantidad_min" class="block mt-1 w-full" type="number" name="cantidad_min" :value="Request('cantidad_min')" 
                         autofocus />
                    </div>
                    <div class="p-2 shrink-0 w-[40]">
                        <x-input-label for="valor_unidad" :value="__('Valor')" />
                        <x-text-input id="valor_unidad" class="block mt-1 w-full" type="number" name="valor_unidad" :value="Request('valor_unidad')" 
                         autofocus />
                    </div>
                    <div class="p-2 shrink-0 w-[40]">
                        <x-input-label for="tipo" :value="__('Tipo Material')" />
                        <x-select-input 
                            name="tipo" 
                            :options="$tipos" 
                            :selected="Request('tipo')" 
                            class="block mt-1 w-full"
                        />
                    </div>
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
                        <x-input-label for="rango" :value="__('Rango Material')" />
                        <select name="rango" id="rango" class="block mt-1 w-full border-gray-300 
                            focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option class="bg-gray-100" value="" @if(Request('rango') == '') selected @endif>-- Seleccione --</option>
                            <option class="bg-red-200" value="1" @if(Request('rango') == '1') selected @endif>Rojo</option>
                            <option class="bg-orange-200" value="2" @if(Request('rango') == '2') selected @endif>Naranja</option>
                            <option value="3" @if(Request('rango') == '3') selected @endif>Blanco</option>
                        </select>
                    </div>

                    <div class="w-full max-w-full px-2 pt-10 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                        <input type="hidden" name="aprobar" value="0">
                        <label class="inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="aprobar" 
                                value="1" 
                                class="sr-only peer"
                                {{ old('aprobar', $material->aprobar ?? false) ? 'checked' : '' }}
                            >
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer 
                                dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute 
                                after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 
                                peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                                Aprobacion para el Despacho
                            </span>
                        </label>
                        <x-input-error :messages="$errors->get('aprobar')" class="mt-2" />
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