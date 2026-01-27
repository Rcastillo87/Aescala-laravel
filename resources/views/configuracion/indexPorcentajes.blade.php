@extends('layouts.app')

@section('content')

<form method="POST" id='formValorArea' action="{{ route('configuracion.savePorcentajes') }}">
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
                    <select id="importe_año"data-type="porcentajes"
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

        @php
            $suma = 0;
        @endphp

        @forelse($items as $item)
            @php
                $suma += $item['porcentage'];
            @endphp
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
                    <div class="w-full max-w-full px-3 py-1 shrink-0 md:w-6/12 lg:w-6/12 md:flex-0">
                        <x-input-label :value="__('Concepto *')" />
                        <x-text-input class="block mt-1 w-full" type="text" name="items[{{ $loop->index }}][concepto]" 
                            :value="old('items.' . $loop->index . '.concepto', $item['concepto'] ?? '')" required />
                    </div>

                    <div class="w-full max-w-full px-3 py-1 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                        <x-input-label :value="__('Porcentaje *')" />
                        <x-text-input class="block mt-1 w-full" type="number" min="0" step="1" name="items[{{ $loop->index }}][porcentage]" 
                            :value="old('items.' . $loop->index . '.porcentage', $item['porcentage'] ?? '')" required />
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

        @if(!empty($items))
            <!-- Div con la suma de porcentajes -->
            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 dark:bg-gray-800 mt-4">
                <div class="flex justify-between items-center">
                    <span class="font-medium text-gray-700 dark:text-gray-300">Suma total de porcentajes:</span>
                    <span class="text-lg font-bold @if($suma == 100) text-green-600 @else text-red-600 @endif">
                        {{ $suma }}%
                    </span>
                </div>
                @if($suma != 100)
                    <div class="mt-2 text-sm text-red-600 dark:text-red-400">
                        ⚠️ La suma de porcentajes debe ser exactamente 100%
                    </div>
                @else
                    <div class="mt-2 text-sm text-green-600 dark:text-green-400">
                        ✓ La suma de porcentajes es correcta
                    </div>
                @endif
            </div>
        @endif
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

            <div class="w-full max-w-full px-3 py-1 shrink-0 md:w-6/12 lg:w-6/12 md:flex-0">
                <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                    Concepto *
                </label>
                <input type="text"
                    name="items[__INDEX__][concepto]"
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                           focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                    required>
            </div>

            <div class="w-full max-w-full px-3 py-1 md:w-6/12 lg:w-4/12 2xl:w-3/12">
                <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                    Porcentaje *
                </label>
                <input type="number" min="0" step="1"
                    name="items[__INDEX__][porcentage]"
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                           focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
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
    <script src="{{asset('js/configuracion/indexPorcentajes.js')}}"></script>
@endsection