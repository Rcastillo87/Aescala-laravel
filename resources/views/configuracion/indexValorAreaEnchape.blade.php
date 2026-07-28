@extends('layouts.app')

@section('content')

<form method="POST" id='formValorAreaEnchape' action="{{ route('configuracion.saveValorAreaEnchape') }}">
    @csrf
    <input type="hidden" name="select_año" value="{{ request()->route('año') }}">

    <!-- Filtro superior -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-4 mb-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Búsqueda de año -->
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Año de configuración
                </label>
                <select onchange="location = this.value"
                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($años as $año)
                        <option
                            value="{{ route('configuracion.indexValorAreaEnchape', $año) }}"
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
                    <select id="importe_año"data-type="valor-area"
                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
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

        @forelse($items as $item)
            <div class="border border-gray-200 rounded-lg p-3 bg-white space-y-2">

                <!-- Info -->
                <div class="flex justify-between text-xs text-gray-500">
                    <span><strong>ID:</strong> {{ $item['id'] }}</span>
                    <span>
                        <strong>Creado:</strong>
                        {{ $item['createdAt'] ? \Carbon\Carbon::parse($item['createdAt'])->format('Y-m-d') : '' }}
                        |
                        <strong>Editado:</strong>
                        {{ $item['updatedAt'] ? \Carbon\Carbon::parse($item['updatedAt'])->format('Y-m-d') : '' }}
                    </span>
                </div>

                <div class="flex flex-wrap -mx-3">
                    <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item['id'] ?? '' }}">
                    <div class="w-full max-w-full px-3 py-1 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                        <x-input-label :value="__('Área mínima')" />
                        <x-text-input class="block mt-1 w-full" type="number" min="0" step="1" name="items[{{ $loop->index }}][area_min]" 
                            :value="old('items.' . $loop->index . '.area_min', $item['area_min'] ?? '')" required />
                    </div>

                    <div class="w-full max-w-full px-3 py-1 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                        <x-input-label :value="__('Área maxima')" />
                        <x-text-input class="block mt-1 w-full" type="number" min="0" step="1" name="items[{{ $loop->index }}][area_max]" 
                            :value="old('items.' . $loop->index . '.area_max', $item['area_max'] ?? '')" required />
                    </div>
                    
                    <div class="relative w-full max-w-full px-3 py-1 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                        <x-input-label :value="__('Valor intervalo')" />
                        <x-text-input class="block mt-1 w-full moneda-cop" type="number" min="0" step="1" name="items[{{ $loop->index }}][valor_intervalo]" 
                            :value="old('items.' . $loop->index . '.valor_intervalo', $item['valor_intervalo'] ?? '')" required />
                    </div>

                    <div class="w-full px-3 py-1">
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
            <div class="border border-gray-200 rounded-lg p-6 text-center text-sm text-gray-500 bg-white deleteDiv">
                No hay registros.
            </div>
        @endforelse

    </div>

    <!-- Botones -->
    <div class="flex justify-between mt-4">
        <x-secondary-button id="btnAddItem" data-empy="{{ empty($items) ? 1 : 0 }}" type="button">
            + Añadir intervalo
        </x-secondary-button>

        <x-primary-button type="submit">
                Guardar
        </x-primary-button>
    </div>
</form>

<template id="item-template">
    <div class="border border-gray-200 rounded-lg p-3 bg-white space-y-2 item-block">
        <!-- Info -->
        <div class="flex justify-between text-xs text-gray-500">
            <span>
                <strong>ID: Nuevo</strong> 
            </span>
            <button type="button"
                class="top-2 right-2 border border-red-600 py-1 px-2 rounded-lg text-red-500 hover:text-red-700 text-xs font-normal remove-item"
                title="Eliminar intervalo">
                ✕
            </button>
        </div>

        <div class="flex flex-wrap -mx-3">
            <input type="hidden" name="items[__INDEX__][id]" value="">

            <div class="w-full max-w-full px-3 py-1 md:w-6/12 lg:w-4/12 2xl:w-3/12">
                <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                    Área mínima
                </label>
                <input type="number" min="0" step="1"
                    name="items[__INDEX__][area_min]"
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                           focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                    required>
            </div>

            <div class="w-full max-w-full px-3 py-1 md:w-6/12 lg:w-4/12 2xl:w-3/12">
                <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                    Área maxima
                </label>
                <input type="number" min="0" step="1"
                    name="items[__INDEX__][area_max]"
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                           focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                    required>
            </div>

            <div class="w-full max-w-full px-3 py-1 md:w-6/12 lg:w-4/12 2xl:w-3/12">
                <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                    Valor intervalo
                </label>
                <input type="number" min="0" step="1"
                    name="items[__INDEX__][valor_intervalo]"
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                           focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full moneda-cop"
                    required>
            </div>

            <div class="w-full px-3 py-1">
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">
                    Descripción
                </label>
                <textarea rows="2"
                    name="items[__INDEX__][descripccion]"
                    class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-700
                           dark:bg-gray-900 dark:text-gray-200 text-sm
                           focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
        </div>
    </div>
</template>

@endsection

@section('scripts')
    <script src="{{ asset('js/configuracion/indexValorAreaEnchape.js') }}?v={{ filemtime(public_path('js/configuracion/indexValorAreaEnchape.js')) }}"></script>
@endsection