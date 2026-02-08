@extends('layouts.app')
@section('content')

    <div class="w-full p-2 max-h-full overflow-y-scroll">
        <form method="POST" action="{{ route('pedidos.save') }}">
            @csrf
            <div class="flex flex-col xl:flex-row gap-4">
                <!-- Primera columna -->
                <div class="w-full xl:w-1/2 space-y-4 p-3 border-2 border-gray-400 rounded-2xl">
                    <div>
                        <input class="hidden" value="{{ json_encode($materiales) }}"  id="arrayMateriales" name="arrayMateriales" disabled>
                        <x-input-label for="id_material" :value="__('Seleccione Material *')" />
                        <x-select-input 
                            placeholder="Busqueda.."
                            autocomplete="off"
                            name="id_material" 
                            id="id_material"
                            :options="$materiales" 
                            :data="['id', 'nombre_material']"
                            :selected="old('id_material')" 
                            class="block mt-1 w-full"
                        />
                        <x-input-error :messages="$errors->get('id_material')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="fec_inicio" :value="__('Fecha Pedido *')" />
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                </svg>
                            </div>
                            <input 
                                datepicker=""
                                datepicker-format="yyyy-mm-dd"
                                autocomplete="off"
                                type="text" 
                                id="fecha" 
                                datepicker-max-date="{{ date('Y-m-d') }}" 
                                name="fecha" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                placeholder="Seleccione fecha"
                                value="{{ old('fecha') }}" 
                                required 
                            />
                        </div>
                        <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="codigo" :value="__('Num Orden o Factura *')" />
                        <x-text-input id="codigo" class="block mt-1 w-full" type="text" name="codigo" :value="old('codigo')" 
                        autofocus required/>
                        <x-input-error :messages="$errors->get('codigo')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="id_proyecto" :value="__('Proveedor *')" />
                        <x-select-input 
                            name="id_proveedor" 
                            id="id_proveedor"
                            :options="$proveedor" 
                            :data="['id', 'razon_social']"
                            :selected="old('id_proveedor')" 
                            class="block mt-1 w-full"
                            required
                        />
                        <x-input-error :messages="$errors->get('id_proveedor')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="id_proyecto" :value="__('Proyecto')" />
                        <x-select-input 
                            name="id_proyecto" 
                            id="id_proyecto"
                            :options="$proyectos" 
                            :data="['id', 'nombre_proyecto']"
                            :selected="old('id_proyecto')" 
                            class="block mt-1 w-full"
                        />
                        <x-input-error :messages="$errors->get('id_proyecto')" class="mt-2" />
                    </div>
                </div>

                <!-- Segunda columna -->
                <div class="w-full h-full xl:w-1/2 text-center border-2 border-gray-400 rounded-2xl">
                    <p class="font-bold text-xl mb-3">Materiales Pedidos</p>
                    <div id="selectMateriales"></div>
                </div>
            </div>

            <!-- Botón alineado a la derecha siempre en la parte inferior -->
            <div class="flex justify-end mt-6">
                <x-secondary-button class="ms-4" href="{{ route('pedidos.index') }}">
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
    <script src="{{ asset('js/pedidos/create.js') }}?v={{ filemtime(public_path('js/pedidos/create.js')) }}"></script>
@endsection