@extends('layouts.app')
@section('content')
<div class="w-full px-2">
    <form method="POST" action="{{ route('material.save') }}">
        @csrf
        <div class="flex flex-wrap -mx-3">
            <input type="hidden" id="id" name="id" value="{{$material?$material->id:''}}">
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="nombre_material" :value="__('Nombre Material *')" />
                <x-text-input id="nombre_material" class="block mt-1 w-full" type="text" name="nombre_material" :value="old('nombre_material', $material?$material->nombre_material:'')" 
                required autofocus />
                <x-input-error :messages="$errors->get('nombre_material')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="cantidad" :value="__('Cantidad *')" />
                <x-text-input id="cantidad" class="block mt-1 w-full" step="any" type="number" name="cantidad" :value="old('cantidad', $material?$material->cantidad:'')" required/>
                <x-input-error :messages="$errors->get('cantidad')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="id_unidad" :value="__('Unidades *')" />
                <x-select-input 
                    name="id_unidad" 
                    :options="$unidades" 
                    :selected="old('id_unidad',$material?$material->id_unidad:'')" 
                    class="block mt-1 w-full" 
                    required
                />
                <x-input-error :messages="$errors->get('id_unidad')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="cantidad_min" :value="__('Cantidad Minima *')" />
                <x-text-input id="cantidad_min" class="block mt-1 w-full" step="any" type="number" name="cantidad_min" :value="old('cantidad_min', $material?$material->cantidad_min:'')" required/>
                <x-input-error :messages="$errors->get('cantidad_min')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="valor_unidad" :value="__('Valor Unidad *')" />
                <x-text-input id="valor_unidad" class="block mt-1 w-full moneda-cop" step="any" type="number" name="valor_unidad" :value="old('valor_unidad', $material?$material->valor_unidad:'')" required/>
                <x-input-error :messages="$errors->get('valor_unidad')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="tipo" :value="__('Tipo Material')" />
                <x-select-input 
                    name="tipo" 
                    :options="$tipos" 
                    :selected="old('tipo', $material?$material->tipo:'')" 
                    class="block mt-1 w-full" 
                />
                <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="descripccion" :value="__('Descripción')" />
                <x-text-input id="descripccion" class="block mt-1 w-full" type="text" name="descripccion" :value="old('descripccion', $material?$material->descripccion:'')"/>
                <x-input-error :messages="$errors->get('descripccion')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-secondary-button class="ms-4" href="{{ route('material.index') }}">
                Atras
            </x-secondary-button>
            <x-primary-button class="ms-4">
                Guardar
            </x-primary-button>
        </div>
    </form>
</div>
@endsection
