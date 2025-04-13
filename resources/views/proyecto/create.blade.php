@extends('layouts.app')
@section('content')
<div class="w-full px-2">
    <form method="POST" action="{{ route('proyecto.save') }}">
        @csrf

        <div class="flex flex-wrap -mx-3">
            <input type="hidden" id="id" name="id" value="{{$proyecto?$proyecto->id:''}}">
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="nombre_proyecto" :value="__('Nombre Proyecto *')" />
                <x-text-input id="nombre_proyecto" class="block mt-1 w-full" type="text" name="nombre_proyecto" 
                :value="old('nombre_proyecto', $proyecto?$proyecto->nombre_proyecto:'')" 
                required autofocus />
                <x-input-error :messages="$errors->get('nombre_proyecto')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="direccion" :value="__('Dirección *')" />
                <x-text-input id="direccion" class="block mt-1 w-full" type="text" name="direccion" :value="old('direccion', $proyecto?$proyecto->direccion:'')" 
                required autofocus />
                <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="nombre_cliente" :value="__('Nombre Cliente *')" />
                <x-text-input id="nombre_cliente" class="block mt-1 w-full" type="text" name="nombre_cliente" :value="old('nombre_cliente', $proyecto?$proyecto->nombre_cliente:'')" 
                required autofocus />
                <x-input-error :messages="$errors->get('nombre_cliente')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="telefono_cliente" :value="__('Telefono Cliente *')" />
                <x-text-input id="telefono_cliente" class="block mt-1 w-full" type="text" name="telefono_cliente" :value="old('telefono_cliente', $proyecto?$proyecto->telefono_cliente:'')" required/>
                <x-input-error :messages="$errors->get('telefono_cliente')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="val_obra_blanca" :value="__('Costo Obra Blanca')" />
                <x-text-input id="val_obra_blanca" class="block mt-1 w-full" type="number" name="val_obra_blanca" :value="old('val_obra_blanca', 
                $proyecto?$proyecto->val_obra_blanca:'')"/>
                <x-input-error :messages="$errors->get('val_obra_blanca')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="val_obra_blanca_materiales" :value="__('Costo Material Obra Blanca')" />
                <x-text-input id="val_obra_blanca_materiales" class="block mt-1 w-full" type="number" name="val_obra_blanca_materiales" 
                :value="old('val_obra_blanca_materiales', $proyecto?$proyecto->val_obra_blanca_materiales:'')"/>
                <x-input-error :messages="$errors->get('val_obra_blanca_materiales')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="val_obra_carpinteria" :value="__('Costo Carpinteria')" />
                <x-text-input id="val_obra_carpinteria" class="block mt-1 w-full" type="number" name="val_obra_carpinteria" :value="old('val_obra_carpinteria', 
                $proyecto?$proyecto->val_obra_carpinteria:'')"/>
                <x-input-error :messages="$errors->get('val_obra_carpinteria')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="val_carpinteria_materiales" :value="__('Costo Material Carpinteria')" />
                <x-text-input id="val_carpinteria_materiales" class="block mt-1 w-full" type="number" name="val_carpinteria_materiales" 
                :value="old('val_carpinteria_materiales', $proyecto?$proyecto->val_carpinteria_materiales:'')"/>
                <x-input-error :messages="$errors->get('val_carpinteria_materiales')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="pres_otros" :value="__('Otros Costos')" />
                <x-text-input id="pres_otros" class="block mt-1 w-full" type="number" name="pres_otros" :value="old('pres_otros', 
                $proyecto?$proyecto->pres_otros:'')"/>
                <x-input-error :messages="$errors->get('pres_otros')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="observacion" :value="__('Comentario')" />
                <x-text-input id="observacion" class="block mt-1 w-full" type="text" name="observacion" :value="old('observacion', $proyecto?$proyecto->observacion:'')" 
                autofocus />
                <x-input-error :messages="$errors->get('observacion')" class="mt-2" />
            </div>

            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="fec_inicio" :value="__('Fecha Ini Proyecto *')" />
                <div class="relative max-w-sm">
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
                        id="fec_inicio" 
                        name="fec_inicio" 
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                        placeholder="Seleccione fecha"
                        value="{{ old('fec_inicio', $proyecto?$proyecto->fec_inicio:'') }}" 
                        required 
                    />
                </div>
                <x-input-error :messages="$errors->get('fec_inicio')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="fec_fin_estimado" :value="__('Fecha Fin Estimado *')" />
                <div class="relative max-w-sm">
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
                        id="fec_fin_estimado" 
                        name="fec_fin_estimado" 
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                        placeholder="Seleccione fecha"
                        value="{{ old('fec_fin_estimado', $proyecto?$proyecto->fec_fin_estimado:'') }}" 
                        required 
                    />
                </div>
                <x-input-error :messages="$errors->get('fec_fin_estimado')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="fec_fin_real" :value="__('Fecha Fin Real')" />
                <div class="relative max-w-sm">
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
                        id="fec_fin_real" 
                        name="fec_fin_real" 
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                        placeholder="Selecciona una fecha"
                        value="{{ old('fec_fin_real', $proyecto?$proyecto->fec_fin_real:'') }}"
                    />
                </div>
                <x-input-error :messages="$errors->get('fec_fin_real')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="id_user" :value="__('Colaborador Encargado *')" />
                <x-select-input 
                    name="id_user" 
                    id="id_user"
                    :options="$colaUsers" 
                    :data="['id', 'nombre_completo']"
                    :selected="old('id_user', $proyecto?$proyecto->id_user:'')" 
                    class="block mt-1 w-full" 
                    required
                />
                <x-input-error :messages="$errors->get('id_user')" class="mt-2" />
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
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-secondary-button class="ms-4" href="{{ route('proyecto.index') }}">
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
    <script src="{{asset('js/proyecto/create.js')}}"></script>
    <script>
        window.departamentos = JSON.parse(@json($departamentos));
    </script>
@endsection