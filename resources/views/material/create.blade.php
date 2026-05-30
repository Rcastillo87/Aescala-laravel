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
                <x-input-label for="codigo" :value="__('Codigo Material')" />
                <x-text-input id="codigo" class="block mt-1 w-full" type="text" name="codigo" :value="old('codigo', $material?$material->codigo:'')" />
                <x-input-error :messages="$errors->get('codigo')" class="mt-2" />
            </div>

            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="cantidad" :value="__('Cantidad *')" />
                <x-text-input id="cantidad" class="block mt-1 w-full" step="any" type="number" name="cantidad" :value="old('cantidad', $material?$material->cantidad:'')" required/>
                <x-input-error :messages="$errors->get('cantidad')" class="mt-2" />
            </div>

            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="cantidad_min" :value="__('Cantidad Minima *')" />
                <x-text-input id="cantidad_min" class="block mt-1 w-full" step="any" type="number" name="cantidad_min" :value="old('cantidad_min', $material?$material->cantidad_min:'')" required/>
                <x-input-error :messages="$errors->get('cantidad_min')" class="mt-2" />
            </div>

            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="id_unidad" :value="__('Unidad de medida *')" />
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
                <x-input-label for="valor_unidad" :value="__('Valor venta *')" />
                <x-text-input id="valor_unidad" class="block mt-1 w-full moneda-cop" min="0" step="any" type="number" name="valor_unidad" :value="old('valor_unidad', $material?$material->valor_unidad:'')" required/>
                <x-input-error :messages="$errors->get('valor_unidad')" class="mt-2" />
            </div>

            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="valor_inventario" :value="__('Valor inventario')" />
                <x-text-input id="valor_inventario" class="block mt-1 w-full moneda-cop" min="0" step="any" type="number" name="valor_inventario" :value="old('valor_inventario', $material?$material->valor_inventario:'')"/>
                <x-input-error :messages="$errors->get('valor_inventario')" class="mt-2" />
            </div>

            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="id_proveedor" :value="__('Proveedor Principal')" />
                <x-select-input
                    id="id_proveedor"
                    name="id_proveedor"
                    :options="$proveedores"
                    :data="['id', 'razon_social']"
                    :selected="old('id_proveedor',$material?$material->id_proveedor:'')"
                    class="block mt-1 w-full"
                />
                <x-input-error :messages="$errors->get('id_proveedor')" class="mt-2" />
            </div>

            @if (!$tipo)
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
            @else
                <input type="hidden" name="tipo" value="{{$tipo}}">
            @endif

            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="zona" :value="__('Zona')" />
                <x-select-input
                    name="zona"
                    :options="$zonas"
                    :selected="old('zona', $material?$material->zona:'')"
                    class="block mt-1 w-full"
                />
            </div>

            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="zona" :value="__('Fase')" />
                <x-select-input
                    name="fase"
                    :options="$fases"
                    :selected="old('zona', $material?$material->fase:'')"
                    class="block mt-1 w-full"
                />
            </div>

            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="descripccion" :value="__('Descripción')" />
                <x-text-input id="descripccion" class="block mt-1 w-full" type="text" name="descripccion" :value="old('descripccion', $material?$material->descripccion:'')"/>
                <x-input-error :messages="$errors->get('descripccion')" class="mt-2" />
            </div>

            <div class="w-full max-w-full px-3 pt-8 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
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
                        Requiere Aprobacion para el Despacho
                    </span>
                </label>
                <x-input-error :messages="$errors->get('aprobar')" class="mt-2" />
            </div>

        </div>

        <div class="flex items-center justify-end mt-4">
            <x-secondary-button class="ms-4" href="{{ session('solicitud_anterior_url') }}">
                Atras
            </x-secondary-button>
            <x-primary-button class="ms-4">
                Guardar
            </x-primary-button>
        </div>
    </form>
</div>
@endsection
@section('scripts')
    <script src="{{ asset('js/material/create.js') }}?v={{ filemtime(public_path('js/material/create.js')) }}"></script>
@endsection
