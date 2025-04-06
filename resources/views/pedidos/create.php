@extends('layouts.app')
@section('content')

    <div class="w-full px-2 max-h-full overflow-y-scroll">
        <form method="POST" action="{{ route('pedidos.save') }}">
            @csrf
            <div class="flex flex-col lg:flex-row gap-4">
                <!-- Primera columna -->
                <div class="w-full lg:w-1/2 space-y-4 p-3 border-2 border-gray-400 rounded-2xl">
                    <div>
                        <x-input-label for="id_proyecto" :value="__('Proveedor *')" />
                        <x-select-input 
                            name="id_proveedor" 
                            id="id_proveedor"
                            :options="$proveedor" 
                            :data="['id', 'razon_social']"
                            :selected="old('id_proveedor')" 
                            class="block mt-1 w-full"
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
                        <x-input-error :messages="$errors->get('id_user')" class="mt-2" />
                    </div>
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
                </div>

                <!-- Segunda columna -->
                <div class="w-full h-full lg:w-1/2 text-center border-2 border-gray-400 rounded-2xl">
                    <p class="font-bold text-xl mb-3">Materiales Pedidos</p>
                    <div id="selectMateriales"></div>
                </div>
            </div>

            <!-- Botón alineado a la derecha siempre en la parte inferior -->
            <div class="flex justify-end mt-6">
                <x-primary-button>
                    Guardar
                </x-primary-button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/pedidos/index.js') }}"></script>
@endsection