@extends('layouts.app')
@section('content')
<div class="w-full px-2">
    <form method="POST" action="{{ route('comercial.save') }}" id="formComercial">
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

            <!-- Campo de firma -->
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label :value="__('Firma')" />

                <!-- Vista previa -->
                <div id="firma-preview"
                    class="border border-gray-300 rounded-lg w-full h-40 flex items-center justify-center bg-gray-50 overflow-hidden">
                    @if($proyecto && $proyecto->img_firma)
                        <img src="{{ $proyecto->img_firma }}"
                            alt="Firma previa"
                            class="w-full h-full object-contain">
                    @else
                        <span class="text-gray-400">No se ha añadido firma</span>
                    @endif
                </div>

                <x-secondary-button class="my-2 py-1 px-1"
                    href="#"
                    data-tooltip-target="tooltip-hover-Entregable"
                    data-tooltip-trigger="hover"
                    x-data=""
                    x-on:click="$dispatch('open-modal', 'firma-modal')"
                    >
                    Añadir / Editar Firma
                </x-secondary-button>

                <!-- Input hidden donde guardamos el base64 -->
                <input type="hidden" name="img_firma" id="img_firma"
                    value="{{ old('img_firma', $proyecto ? $proyecto->img_firma : '') }}">

                <x-input-error :messages="$errors->get('img_firma')" class="mt-2" />
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
                <x-input-label for="ubicacion" :value="__('Ubicación *')" />
                <x-select-input
                    name="ubicacion"
                    id="ubicacion"
                    :options="$ubicacion"
                    :selected="old('ubicacion', $proyecto?$proyecto->ubicacion:'')"
                    class="block mt-1 w-full"
                    required
                />
                <x-input-error :messages="$errors->get('ubicacion')" class="mt-2" />
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
                <x-input-label for="area_privada" :value="__('Area Privada(mts cuadrados) *')" />
                <x-text-input id="area_privada" class="block w-full" type="number"
                    min="0" step="any" name="area_privada" value="{{ old('area_privada', $proyecto?->area_privada) }}"/>
                <x-input-error :messages="$errors->get('area_privada')" class="mt-2" />
            </div>

            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="descuento" :value="__('Descuento Aceptado por Gerencia')" />
                <x-text-input id="descuento" class="block w-full" type="number"
                    min="0" step="any" name="descuento" value="{{ old('descuento', $proyecto?->descuento ?? 0) }}"/>
                <x-input-error :messages="$errors->get('descuento')" class="mt-2" />
            </div>

            <div
                x-data="{ acepta: {{ old('acepta_trata_datos', $proyecto?->acepta_trata_datos ?? 0) ? 'true' : 'false' }} }"
                x-on:aceptar-tratamiento.window="acepta = true"
                x-on:revocar-tratamiento.window="acepta = false"
                class="w-full max-w-full px-3 pt-6 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12"
            >
                <!-- Valor real -->
                <input type="hidden" name="acepta_trata_datos" :value="acepta ? 1 : 0">

                <label class="inline-flex items-center space-x-2">
                    <input
                        type="checkbox"
                        x-model="acepta"
                        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                        focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500
                        dark:focus:ring-indigo-600 rounded-md shadow-sm"
                    >

                    <button
                        type="button"
                        class="text-blue-600 underline hover:text-blue-800"
                        x-on:click="$dispatch('open-modal', 'tratamiento-datos-modal')"
                    >
                        Acepto el tratamiento de datos personales
                    </button>
                </label>
            </div>

            <hr class="w-full my-2">
            <div class="mx-auto px-2 py-2 flex justify-start w-full">
                <h2 class="text-xl font-bold text-[#242e68]">Porcentajes </h2>
            </div>

            <div class="w-[50%] max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-2/12 md:flex-0">
                <x-input-label for="termino_1_por" :value="__('Inicio de Diseño(%) *')" />
                <x-text-input id="termino_1_por" class="block w-full" type="number"
                    min="0" step="any" name="termino_1_por" value="{{ old('termino_1_por', $proyecto?->termino_1_por ?? 30) }}"/>
                <x-input-error :messages="$errors->get('termino_1_por')" class="mt-2" />
            </div>
            <div class="w-[50%] max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-2/12 md:flex-0">
                <x-input-label for="termino_2_por" :value="__('Inicio de Obra-Blanca(%) *')" />
                <x-text-input id="termino_2_por" class="block w-full" type="number"
                    min="0" step="any" name="termino_2_por" value="{{ old('termino_2_por', $proyecto?->termino_2_por ?? 20) }}"/>
                <x-input-error :messages="$errors->get('termino_2_por')" class="mt-2" />
            </div>
            <div class="w-[50%] max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-2/12 md:flex-0">
                <x-input-label for="termino_3_por" :value="__('Inicio Corte Carpinteria(%) *')" />
                <x-text-input id="termino_3_por" class="block w-full" type="number"
                    min="0" step="any" name="termino_3_por" value="{{ old('termino_3_por', $proyecto?->termino_3_por ?? 30) }}"/>
                <x-input-error :messages="$errors->get('termino_3_por')" class="mt-2" />
            </div>
            <div class="w-[50%] max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-2/12 md:flex-0">
                <x-input-label for="termino_4_por" :value="__('Instalación Carpinteria(%) *')" />
                <x-text-input id="termino_4_por" class="block w-full" type="number"
                    min="0" step="any" name="termino_4_por" value="{{ old('termino_4_por', $proyecto?->termino_4_por ?? 15) }}"/>
                <x-input-error :messages="$errors->get('termino_4_por')" class="mt-2" />
            </div>
            <div class="w-[50%] max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-2/12 md:flex-0">
                <x-input-label for="termino_5_por" :value="__('Instalación Accesorios(%) *')" />
                <x-text-input id="termino_5_por" class="block w-full" type="number"
                    min="0" step="any" name="termino_5_por" value="{{ old('termino_5_por', $proyecto?->termino_5_por ?? 3) }}"/>
                <x-input-error :messages="$errors->get('termino_5_por')" class="mt-2" />
            </div>
            <div class="w-[50%] max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-2/12 md:flex-0">
                <x-input-label for="termino_6_por" :value="__('Entrega de Obra(%) *')" />
                <x-text-input id="termino_6_por" class="block w-full" type="number"
                    min="0" step="any" name="termino_6_por" value="{{ old('termino_6_por', $proyecto?->termino_6_por ?? 2) }}"/>
                <x-input-error :messages="$errors->get('termino_6_por')" class="mt-2" />
            </div>

            <div class="flex items-center gap-x-4 w-full max-w-full p-3 shrink-0 md:w-12/12 lg:w-6/12 2xl:w-4/12">
                <label class="inline-flex items-center w-[30%] space-x-2">
                    <input type="hidden" name="opcion" id="opcion"  value="{{ old('opcion', $proyecto?->opcion ?? 0) }}">
                    <input
                        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500
                        dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        type="checkbox"
                        id="checkOpcion"
                        {{ old('opcion', $proyecto?->opcion) == 1 ? 'checked' : '' }}
                    />
                    <span class="text-gray-700">Inicia Proyecto</span>
                </label>
                <div id="inputExtra" class="{{ old('opcion', $proyecto?->opcion) == 1 ? '' : 'hidden' }} w-[70%]">
                    <x-input-label for="por_inicia" :value="__('Porcentaje de Inicio(%) *')" />
                    <x-text-input
                        type="number"
                        value="{{ old('por_inicia', $proyecto?->por_inicia ?? '') }}"
                        name="por_inicia"
                        id="por_inicia"
                    />
                </div>
            </div>

            <hr class="w-full my-2">
            <div class="mx-auto px-2 py-2 w-full">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- Entregables --}}
                    <div>
                        <h2 class="text-xl font-bold text-[#242e68] mb-2">Entregables *</h2>
                        <div class="flex w-full items-start space-x-2">
                            <div class="py-1 shrink-0">
                                <x-secondary-button class="my-2 py-1 px-1" href="#"
                                    data-tooltip-target="tooltip-hover-Entregable" data-tooltip-trigger="hover"
                                    x-data="" x-on:click="$dispatch('open-modal', 'entregable-modal')">
                                    <svg class="w-6 h-6 text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
                                    </svg>
                                </x-secondary-button>
                                <div id="tooltip-hover-Entregable" role="tooltip"
                                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                    Añadir Entregable
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            </div>
                            <div id="entregables-div" class="mt-2 space-y-2 w-full">
                                {!! $entregableProye !!}
                            </div>
                        </div>
                    </div>

                    {{-- Notas Personalizadas --}}
                    <div>
                        <h2 class="text-xl font-bold text-[#242e68] mb-2">Notas Personalizadas</h2>
                        <div class="flex w-full items-start space-x-2">
                            <div class="py-1 shrink-0">
                                <x-secondary-button class="my-2 py-1 px-1" href="#"
                                    data-tooltip-target="tooltip-hover-notas" data-tooltip-trigger="hover"
                                    x-data="" x-on:click="$dispatch('open-modal', 'notas-modal')"
                                    id="btn-abrir-notas">
                                    <svg class="w-6 h-6 text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.556 8.5h8m-8 3.5H12m7.111-7H4.89a.896.896 0 0 0-.629.256.868.868 0 0 0-.26.619v9.25c0 .232.094.455.26.619A.896.896 0 0 0 4.89 16H9l3 4 3-4h4.111a.896.896 0 0 0 .629-.256.868.868 0 0 0 .26-.619v-9.25a.868.868 0 0 0-.26-.619.896.896 0 0 0-.63-.256Z"/>
                                    </svg>
                                </x-secondary-button>
                                <div id="tooltip-hover-notas" role="tooltip"
                                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                    Añadir Nota Personalizada
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            </div>

                            {{-- Tarjeta de notas guardadas --}}
                            <div id="notas-div" class="mt-2 w-full hidden">
                                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="font-semibold text-gray-700 text-sm flex items-center gap-1">
                                            <svg class="w-4 h-4 text-[#242e68]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.556 8.5h8m-8 3.5H12m7.111-7H4.89a.896.896 0 0 0-.629.256.868.868 0 0 0-.26.619v9.25c0 .232.094.455.26.619A.896.896 0 0 0 4.89 16H9l3 4 3-4h4.111a.896.896 0 0 0 .629-.256.868.868 0 0 0 .26-.619v-9.25a.868.868 0 0 0-.26-.619.896.896 0 0 0-.63-.256Z"/>
                                            </svg>
                                            Notas (<span id="notas-count">0</span>)
                                        </span>
                                        <div class="relative">
                                            <button type="button" id="btn-editar-notas"
                                                x-data="" x-on:click="$dispatch('open-modal', 'notas-modal')"
                                                data-tooltip-target="tooltip-editar-notas"
                                                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-3 py-2 rounded-lg transition">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                                                </svg>
                                                Editar notas
                                            </button>
                                            <div id="tooltip-editar-notas" role="tooltip"
                                                class="absolute z-10 invisible px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip">
                                                Modificar las notas del contrato
                                                <div class="tooltip-arrow" data-popper-arrow></div>
                                            </div>
                                        </div>
                                    </div>
                                    <ul id="notas-preview" class="list-disc pl-5 space-y-2 text-sm text-gray-600"></ul>
                                    <div id="notas-hidden-inputs"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 2xl:grid-cols-6 gap-4 p-4 rounded-xl border border-gray-200 bg-white shadow">

            <!-- Inicio de Diseño -->
            <div class="p-3 rounded-lg bg-blue-50 border border-blue-100 hover:shadow-md transition">
                <h3 class="font-semibold text-gray-700 text-sm">Inicio de Diseño</h3>
                <p id="termino_1_p" class="text-xs text-gray-500 mt-1">
                    {{ old('termino_1_por', $proyecto?->termino_1_por ?? 30) }}%
                </p>
                <p id="termino_1_spa" class="text-base font-bold text-blue-800">
                    $ {{ number_format(old('termino_1_por', $proyecto?->termino_1_por) * $valor, 2, '.', ',') }}
                </p>
            </div>

            <!-- Inicio de Obra Blanca -->
            <div class="p-3 rounded-lg bg-green-50 border border-green-100 hover:shadow-md transition">
                <h3 class="font-semibold text-gray-700 text-sm">Inicio de Obra Blanca</h3>
                <p id="termino_2_p" class="text-xs text-gray-500 mt-1">
                    {{ old('termino_2_por', $proyecto?->termino_2_por ?? 20) }}%
                </p>
                <p id="termino_2_spa" class="text-base font-bold text-green-800">
                    $ {{ number_format(old('termino_2_por', $proyecto?->termino_2_por) * $valor, 2, '.', ',') }}
                </p>
            </div>

            <!-- Corte Carpinteria -->
            <div class="p-3 rounded-lg bg-purple-50 border border-purple-100 hover:shadow-md transition">
                <h3 class="font-semibold text-gray-700 text-sm">Inicio Corte Carpinteria</h3>
                <p id="termino_3_p" class="text-xs text-gray-500 mt-1">
                    {{ old('termino_3_por', $proyecto?->termino_3_por ?? 30) }}%
                </p>
                <p id="termino_3_spa" class="text-base font-bold text-purple-800">
                    $ {{ number_format(old('termino_3_por', $proyecto?->termino_3_por) * $valor, 2, '.', ',') }}
                </p>
            </div>

            <!-- Instalación Carpinteria -->
            <div class="p-3 rounded-lg bg-yellow-50 border border-yellow-100 hover:shadow-md transition">
                <h3 class="font-semibold text-gray-700 text-sm">Instalación Carpinteria</h3>
                <p id="termino_4_p" class="text-xs text-gray-500 mt-1">
                    {{ old('termino_4_por', $proyecto?->termino_4_por ?? 15) }}%
                </p>
                <p id="termino_4_spa" class="text-base font-bold text-yellow-800">
                    $ {{ number_format(old('termino_4_por', $proyecto?->termino_4_por) * $valor, 2, '.', ',') }}
                </p>
            </div>

            <!-- Instalación Accesorios -->
            <div class="p-3 rounded-lg bg-pink-50 border border-pink-100 hover:shadow-md transition">
                <h3 class="font-semibold text-gray-700 text-sm">Instalación Accesorios</h3>
                <p id="termino_5_p" class="text-xs text-gray-500 mt-1">
                    {{ old('termino_5_por', $proyecto?->termino_5_por ?? 3) }}%
                </p>
                <p id="termino_5_spa" class="text-base font-bold text-pink-800">
                    $ {{ number_format(old('termino_5_por', $proyecto?->termino_5_por) * $valor, 2, '.', ',') }}
                </p>
            </div>

            <!-- Entrega de Obra -->
            <div class="p-3 rounded-lg bg-indigo-50 border border-indigo-100 hover:shadow-md transition">
                <h3 class="font-semibold text-gray-700 text-sm">Entrega de Obra</h3>
                <p id="termino_6_p" class="text-xs text-gray-500 mt-1">
                    {{ old('termino_6_por', $proyecto?->termino_6_por ?? 2) }}%
                </p>
                <p id="termino_6_spa" class="text-base font-bold text-indigo-800">
                    $ {{ number_format(old('termino_6_por', $proyecto?->termino_6_por) * $valor, 2, '.', ',') }}
                </p>
            </div>

            <!-- Totales -->
            <div class="col-span-1 sm:col-span-2 md:col-span-3 2xl:col-span-6 p-4 rounded-lg bg-red-100 border border-red-200 flex flex-col items-center justify-center shadow">
                <h3 class="font-bold text-red-800 text-lg">Totales</h3>
                <p id="total_p" class="text-sm font-medium text-red-700 mt-1">
                    ({{ $suma ?? 100 }}%)
                </p>
                <p id="total_spa" class="text-xl font-extrabold text-red-900">
                    {{$valor}}
                </p>
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
@include('comercial.modalFirma')
@include('comercial.modalTrataDatos')
@include('comercial.modalNotas')
@endsection

@section('scripts')
    <script>
        window.notasIniciales = @json($notas ?? []);
        window.departamentos = JSON.parse(@json($departamentos));
    </script>
    <script>
        const saveUrl = "{{ route('comercial.save') }}";
    </script>
    <script src="{{ asset('js/comercial/signature_pad.umd.min.js') }}"></script>
    <script src="{{ asset('js/comercial/create.js') }}?v={{ filemtime(public_path('js/comercial/create.js')) }}"></script>

@endsection
