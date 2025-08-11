@extends('layouts.app')
@section('content')
<div class="w-full px-2">
    <form method="POST" action="{{ route('comercial.save') }}">
        @csrf
        <div class="flex flex-wrap -mx-3">

            <div class="mx-auto px-2 py-2 flex justify-start w-full">
                <h2 class="text-xl font-bold text-[#242e68]">Informacion Cliente</h2>
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="nombre_cliente" :value="__('Nombre Cliente *')" />
                <x-text-input id="nombre_cliente" class="block mt-1 w-full" type="text" name="nombre_cliente" :value="old('nombre_cliente', $proyecto?$proyecto->nombre_cliente:'')" 
                required autofocus />
                <x-input-error :messages="$errors->get('nombre_cliente')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="tipo_doc_cliente" :value="__('Tipo Doc Cliente *')" />
                <select name="tipo_doc_cliente" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 
                    dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" required>
                    <option value="">-- Seleccione --</option>
                    @foreach ($tipoDocs as $key => $value)
                        <option value="{{$key}}" {{(old('tipo_doc_cliente', $proyecto?$proyecto->tipo_doc_cliente:'') == $key)?'selected':'' }}>
                            {{$value[1]}}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('tipo_doc_cliente')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="cedula_cliente" :value="__('Cedula Cliente *')" />
                <x-text-input id="cedula_cliente" class="block mt-1 w-full" type="number" name="cedula_cliente" :value="old('cedula_cliente', $proyecto?$proyecto->cedula_cliente:'')" required/>
                <x-input-error :messages="$errors->get('cedula_cliente')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="telefono_cliente" :value="__('Telefono Cliente *')" />
                <x-text-input id="telefono_cliente" class="block mt-1 w-full" type="text" name="telefono_cliente" :value="old('telefono_cliente', $proyecto?$proyecto->telefono_cliente:'')" required/>
                <x-input-error :messages="$errors->get('telefono_cliente')" class="mt-2" />
            </div>

            <hr class="w-full my-2">
            <div class="mx-auto px-2 py-2 flex justify-start w-full">
                <h2 class="text-xl font-bold text-[#242e68]">Informacion Proyecto</h2>
            </div>
            <input type="hidden" id="id" name="id" value="{{$proyecto?$proyecto->id:''}}">
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="nombre_proyecto" :value="__('Nombre Proyecto *')" />
                <x-text-input id="nombre_proyecto" class="block mt-1 w-full" type="text" name="nombre_proyecto" 
                :value="old('nombre_proyecto', $proyecto?$proyecto->nombre_proyecto:'')" 
                required autofocus />
                <x-input-error :messages="$errors->get('nombre_proyecto')" class="mt-2" />
            </div>
            @php
                $depts = json_decode($departamentos, true)
            @endphp
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="departamento" :value="__('Departamento *')" />
                <x-select-input 
                    name="departamento" 
                    id="departamento"
                    :options="$depts" 
                    :data="['id', 'departamento']"
                    :selected="old('departamento',$proyecto?$proyecto->departamento:'')" 
                    class="block mt-1 w-full" 
                    required
                />
                <x-input-error :messages="$errors->get('id_user')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="ciudad" :value="__('Ciudad *')" />
                <x-select-input 
                    name="ciudad" 
                    id="ciudad"
                    :options="$ciudades"
                    :selected="old('ciudad', $proyecto?$proyecto->ciudad:'')" 
                    class="block mt-1 w-full" 
                    required
                />
                <x-input-error :messages="$errors->get('ciudad')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="direccion" :value="__('Dirección *')" />
                <x-text-input id="direccion" class="block mt-1 w-full" type="text" name="direccion" :value="old('direccion', $proyecto?$proyecto->direccion:'')" 
                required autofocus />
                <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="dias_trabajo" :value="__('Días Duración del Proyecto *')" />
                <x-text-input id="dias_trabajo" class="block w-full" type="number" 
                                name="dias_trabajo" value="{{ old('dias_trabajo', $proyecto?->dias_trabajo ?? 1) }}"/>
                <x-input-error :messages="$errors->get('dias_trabajo')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="area_privada" :value="__('Area Privada *')" />
                <x-text-input id="area_privada" class="block w-full" type="number" 
                    min="0" step="any" name="area_privada" value="{{ old('area_privada', $proyecto?->area_privada ?? 0) }}"/>
                <x-input-error :messages="$errors->get('area_privada')" class="mt-2" />
            </div>

            <hr class="w-full my-2">
            <div class="mx-auto px-2 py-2 justify-start w-full">
                <h2 class="text-xl font-bold text-[#242e68]">Entregables</h2>
                <x-secondary-button class="my-2 py-1 px-1" href="#" data-tooltip-target="tooltip-hover-Entregable" data-tooltip-trigger="hover" 
                    x-data="" x-on:click="$dispatch('open-modal', 'entregable-modal')">
                    <svg class="w-6 h-6 text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
                    </svg>
                </x-secondary-button>
                <div id="tooltip-hover-Entregable" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                    Añadir Entregable
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end mt-4">
            <x-secondary-button class="ms-4" href="{{ route('comercial.index') }}">
                Atras
            </x-secondary-button>
            <x-primary-button class="ms-4">
                Guardar
            </x-primary-button>
        </div>
    </form>
</div>
@include('comercial.modalEntregable')
@endsection

@section('scripts')
    <script src="{{asset('js/proyecto/create.js')}}"></script>
    <script>
        window.departamentos = JSON.parse(@json($departamentos));
    </script>
@endsection