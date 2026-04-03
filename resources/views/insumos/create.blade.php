@extends('layouts.app')
@section('content')
<div class="w-full px-2">
    <form method="POST" action="{{ route('insumos.save') }}" autocomplete="off">
        @csrf
        <div class="flex flex-wrap -mx-3">

            <input type="hidden" id="id" name="id" value="{{$item?$item->id:''}}">

            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="nombre_insumo" :value="__('Nombre Insumo *')" />
                <x-text-input id="nombre_insumo" class="block mt-1 w-full" type="text" name="nombre_insumo" :value="old('nombre_insumo', $item?$item->nombre_insumo:'')"
                required autofocus />
                <x-input-error :messages="$errors->get('nombre_insumo')" class="mt-2" />
            </div>

            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="codigo" :value="__('Codigo *')" />
                <x-text-input id="codigo" class="block mt-1 w-full" type="text" name="codigo" :value="old('codigo', $item?$item->codigo:'')"
                required autofocus />
                <x-input-error :messages="$errors->get('codigo')" class="mt-2" />
            </div>

            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="cantidad" :value="__('Cantidad *')" />
                <x-text-input id="cantidad" class="block mt-1 w-full" type="number" name="cantidad" :value="old('cantidad', $item?$item->cantidad:'')" required/>
                <x-input-error :messages="$errors->get('cantidad')" class="mt-2" />
            </div>

            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="cantidad_min" :value="__('Cantidad Minima *')" />
                <x-text-input id="cantidad_min" class="block mt-1 w-full" type="number" name="cantidad_min" :value="old('cantidad_min', $item?$item->cantidad_min:'')" required/>
                <x-input-error :messages="$errors->get('cantidad_min')" class="mt-2" />
            </div>

            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="descripccion" :value="__('Descripccion')" />
                <textarea name="descripccion" id="descripccion" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500
                    dark:focus:ring-indigo-600 rounded-md shadow-sm item-input block w-full">{{old('cantidad_min', $item?$item->descripccion:'')}}</textarea>
            </div>

        </div>

        <div class="flex items-center justify-end mt-4">
            <x-secondary-button class="ms-4" href="{{ route('insumos.index') }}">
                Atras
            </x-secondary-button>
            <x-primary-button class="ms-4">
                {{ __($action) }}
            </x-primary-button>
        </div>
    </form>
</div>
@endsection
