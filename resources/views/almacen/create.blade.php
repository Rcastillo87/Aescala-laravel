@extends('layouts.app')
@section('content')
<div class="w-full px-2">
    <form method="POST" action="{{ route('almacen.save') }}" autocomplete="off">
        @csrf
        <div class="flex flex-wrap -mx-3">
            <input type="hidden" id="id" name="id" value="{{$data?$data->id:''}}">

            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="nombre_almacen" :value="__('Nombre Completo *')" />
                <x-text-input id="nombre_almacen" class="block mt-1 w-full" type="text" name="nombre_almacen" :value="old('nombre_almacen', $data?$data->nombre_almacen:'')"
                required autofocus />
                <x-input-error :messages="$errors->get('nombre_almacen')" class="mt-2" />
            </div>

            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="id_user" :value="__('Encargado Almacen *')" />
                <x-select-input
                    name="id_user"
                    :data="['id', 'nombre_completo']"
                    :options="$users"
                    :selected="Request('id_user', $data?$data->id_user:'')"
                    class="block mt-1 w-full"
                    required
                />
                <x-input-error :messages="$errors->get('id_user')" class="mt-2" />
            </div>

            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="tipo" :value="__('Tipo Material')" />
                <x-select-input
                    name="tipo"
                    :options="$tipos"
                    :selected="old('tipo', $data?$data->tipo:'')"
                    class="block mt-1 w-full"
                />
                <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
            </div>

        <div class="flex items-center justify-end mt-4">
            <x-secondary-button class="ms-4" href="{{ route('almacen.index') }}">
                Atras
            </x-secondary-button>
            <x-primary-button class="ms-4">
                Guardar
            </x-primary-button>
        </div>
    </form>
</div>
@endsection
