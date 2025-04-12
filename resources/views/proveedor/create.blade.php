@extends('layouts.app')
@section('content')
<div class="w-full px-2">
    <form method="POST" action="{{ route('proveedor.save') }}">
        @csrf
        <div class="flex flex-wrap -mx-3">
            <input type="hidden" id="id" name="id" value="{{$proveedor?$proveedor->id:''}}">
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="razon_social" :value="__('Nombre | Razón *')" />
                <x-text-input id="razon_social" class="block mt-1 w-full" type="text" name="razon_social" :value="old('razon_social', $proveedor?$proveedor->razon_social:'')" 
                required autofocus />
                <x-input-error :messages="$errors->get('razon_social')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="nit" :value="__('Documento | NIT *')" />
                <x-text-input id="nit" class="block mt-1 w-full" type="text" name="nit" :value="old('nit', $proveedor?$proveedor->nit:'')" required/>
                <x-input-error :messages="$errors->get('nit')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="direccion" :value="__('Direccion')" />
                <x-text-input id="direccion" class="block mt-1 w-full" type="text" name="direccion" :value="old('direccion', $proveedor?$proveedor->direccion:'')" />
                <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="telefono" :value="__('Telefono')" />
                <x-text-input id="telefono" class="block mt-1 w-full" type="text" name="telefono" :value="old('telefono', $proveedor?$proveedor->telefono:'')" required/>
                <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
            </div>
        </div>
        <div class="flex items-center justify-end mt-4">
            <x-secondary-button class="ms-4" href="{{ route('proveedor.index') }}">
                Atras
            </x-secondary-button>
            <x-primary-button class="ms-4">
                Guardar
            </x-primary-button>
        </div>
    </form>
</div>
@endsection
