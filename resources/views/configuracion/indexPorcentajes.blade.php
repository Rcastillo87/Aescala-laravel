@extends('layouts.app')

@section('content')

<form method="POST" id='formValorArea' action="{{ route('configuracion.savePorcentajes') }}">
    @csrf
    <input type="hidden" name="select_año" value="{{ request()->route('año') }}">

    <!-- Filtro superior -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-4 mb-6 space-y-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Búsqueda de año -->
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Año de configuración
                </label>
                <select onchange="location = this.value"
                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    @foreach($años as $año)
                        <option
                            value="{{ route('configuracion.indexPorcentajes', $año) }}"
                            @selected(request()->route('año') == $año)
                        >{{ $año }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Importar año -->
            @if(empty($items))
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Importar configuración desde
                    </label>
                    <select id="importe_año" data-type="porcentajes"
                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">-- Seleccione --</option>
                        @foreach($años0 as $año)
                            <option value="{{ $año }}">{{ $año }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>

    <!-- Items -->
    <div class="space-y-4" id="importarAño">

        @php
            $suma = 0;
        @endphp

        @forelse($items as $item)
            @php
                if( empty($item['en_pesos'] == 0) ) {
                    $suma += $item['porcentage'];
                }
            @endphp
            <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-900 space-y-3 shadow-sm transition-all hover:shadow">

                <!-- Info -->
                <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-800 pb-2">
                    <span><strong>ID:</strong> {{ $item['id'] }}</span>
                    <span>
                        <strong>Creado:</strong>
                        {{ $item['createdAt'] ? \Carbon\Carbon::parse($item['createdAt'])->format('Y-m-d') : '' }}
                        |
                        <strong>Editado:</strong>
                        {{ $item['updatedAt'] ? \Carbon\Carbon::parse($item['updatedAt'])->format('Y-m-d') : '' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                    <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item['id'] ?? '' }}">
                    
                    <!-- Concepto -->
                    <div class="md:col-span-6">
                        <x-input-label :value="__('Concepto *')" />
                        <x-text-input class="block mt-1 w-full text-sm" type="text" name="items[{{ $loop->index }}][concepto]" 
                            :value="old('items.' . $loop->index . '.concepto', $item['concepto'] ?? '')" required />
                    </div>

                    <!-- Porcentaje y Checkbox alineados -->
                    <div class="md:col-span-6 grid grid-cols-1 sm:grid-cols-2 gap-3 items-end">
                        <div>
                            <!-- Agregamos la clase 'label-porcentaje' aquí -->
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300 label-porcentaje">
                                {{ (old('items.' . $loop->index . '.en_pesos', $item['en_pesos'] ?? false)) ? 'Pesos *' : 'Porcentaje *' }}
                            </label>
                            <x-text-input class="block mt-1 w-full text-sm" type="number" min="0" step="1" name="items[{{ $loop->index }}][porcentage]" 
                                :value="old('items.' . $loop->index . '.porcentage', $item['porcentage'] ?? '')" required />
                        </div>

                        <!-- Checkbox con la clase 'toggle-pesos' -->
                        <div class="flex items-center h-10 px-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg justify-between">
                            <label for="en_pesos_{{ $loop->index }}" class="text-xs font-medium text-gray-700 dark:text-gray-300 cursor-pointer select-none">
                                ¿En Pesos?
                            </label>
                            <input type="checkbox" id="en_pesos_{{ $loop->index }}" name="items[{{ $loop->index }}][en_pesos]" value="1" 
                                class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 toggle-pesos cursor-pointer"
                                @if(old('items.' . $loop->index . '.en_pesos', $item['en_pesos'] ?? false)) checked @endif
                            />
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="md:col-span-12">
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">
                            Descripción
                        </label>
                        <textarea
                            rows="2"
                            name="items[{{ $loop->index }}][descripccion]"
                            class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        >{{ $item['descripccion'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        @empty
            <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-8 text-center text-sm text-gray-500 bg-white dark:bg-gray-900 deleteDiv shadow-sm">
                No hay registros configurados.
            </div>
        @endforelse

        @if(!empty($items))
            <!-- Div con la suma de porcentajes -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-gray-50 dark:bg-gray-800/50 mt-4 shadow-sm">
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700 dark:text-gray-300 text-sm">Suma total de porcentajes:</span>
                    <span class="text-base font-bold @if($suma == 100) text-green-600 dark:text-green-400 @else text-red-600 dark:text-red-400 @endif">
                        {{ $suma }}%
                    </span>
                </div>
                @if($suma != 100)
                    <div class="mt-2 text-xs text-red-600 dark:text-red-400 font-medium">
                        ⚠️ La suma de porcentajes debe ser exactamente 100%
                    </div>
                @else
                    <div class="mt-2 text-xs text-green-600 dark:text-green-400 font-medium">
                        ✓ La suma de porcentajes es correcta
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Botones -->
    <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200 dark:border-gray-800">
        <x-secondary-button id="btnAddItem" data-empy="{{ empty($items) ? 1 : 0 }}" type="button">
            + Añadir intervalo
        </x-secondary-button>

        <x-primary-button type="submit">
            Guardar cambios
        </x-primary-button>
    </div>
</form>

<template id="item-template">
    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-900 space-y-3 item-block shadow-sm">
        <!-- Info -->
        <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-800 pb-2">
            <span><strong>ID: Nuevo</strong></span>
            <button type="button"
                class="border border-red-200 hover:border-red-600 py-0.5 px-2 rounded-lg text-red-500 hover:text-white hover:bg-red-600 text-xs font-medium transition-colors remove-item"
                title="Eliminar intervalo">
                ✕ Eliminar
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
            <input type="hidden" name="items[__INDEX__][id]" value="">

            <!-- Concepto -->
            <div class="md:col-span-6">
                <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                    Concepto *
                </label>
                <input type="text"
                    name="items[__INDEX__][concepto]"
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full text-sm"
                    required>
            </div>

            <!-- Porcentaje y Checkbox alineados -->
            <div class="md:col-span-6 grid grid-cols-1 sm:grid-cols-2 gap-3 items-end">
                <div>
                    <!-- 🛠️ AGREGADA LA CLASE 'label-porcentaje' AQUÍ -->
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300 label-porcentaje">
                        Porcentaje *
                    </label>
                    <input type="number" min="0" step="1"
                        name="items[__INDEX__][porcentage]"
                        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full text-sm"
                        required>
                </div>

                <!-- Checkbox Rediseñado Compacto -->
                <div class="flex items-center h-10 px-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg justify-between">
                    <label class="text-xs font-medium text-gray-700 dark:text-gray-300 cursor-pointer select-none">
                        ¿En Pesos?
                    </label>
                    <!-- 🛠️ AGREGADA LA CLASE 'toggle-pesos' AQUÍ -->
                    <input type="checkbox"
                        name="items[__INDEX__][en_pesos]"
                        value="1"
                        class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 cursor-pointer toggle-pesos">
                </div>
            </div>

            <!-- Descripción -->
            <div class="md:col-span-12">
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">
                    Descripción
                </label>
                <textarea rows="2"
                    name="items[__INDEX__][descripccion]"
                    class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
        </div>
    </div>
</template>

@endsection

@section('scripts')
    <script src="{{ asset('js/configuracion/indexPorcentajes.js') }}?v={{ filemtime(public_path('js/configuracion/indexPorcentajes.js')) }}"></script>
@endsection