@extends('layouts.app')
@section('content')
<div class="w-full px-2">
    <form method="POST" action="{{ route('herramienta.save') }}">
        @csrf
        <div class="flex flex-wrap -mx-3">
            <input type="hidden" id="id" name="id" value="{{$herra?$herra->id:''}}">
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="nombre_herramienta" :value="__('Nombre Herramienta *')" />
                <x-text-input id="nombre_herramienta" class="block mt-1 w-full" type="text" name="nombre_herramienta" :value="old('nombre_herramienta', $herra?$herra->nombre_herramienta:'')" 
                required autofocus />
                <x-input-error :messages="$errors->get('nombre_completo')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="referencia" :value="__('Referencia *')" />
                <x-text-input id="referencia" class="block mt-1 w-full" type="text" name="referencia" :value="old('referencia', $herra?$herra->referencia:'')" required/>
                <x-input-error :messages="$errors->get('referencia')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="observacion" :value="__('Observacion')" />
                <x-text-input id="observacion" class="block mt-1 w-full" type="text" name="observacion" :value="old('observacion', $herra?$herra->observacion:'')" />
                <x-input-error :messages="$errors->get('observacion')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="marca" :value="__('Marca *')" />
                <x-text-input id="marca" class="block mt-1 w-full" type="text" name="marca" :value="old('marca', $herra?$herra->marca:'')" required/>
                <x-input-error :messages="$errors->get('marca')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="estado" :value="__('Estado *')" />
                <select name="estado" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 
                    dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" required>
                    <option value="">-- Seleccione --</option>
                    @foreach ($estado as $key => $value)
                        <option value="{{$key}}" {{(old('estado', $herra?$herra->estado:'') == $key)?'selected':'' }}>
                            {{$value}}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('tipo_documento')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-secondary-button class="ms-4" href="{{ route('herramienta.index') }}">
                Atras
            </x-secondary-button>
            <x-primary-button class="ms-4">
                {{ __($action) }}
            </x-primary-button>
        </div>
    </form>
    </div>
@endsection
