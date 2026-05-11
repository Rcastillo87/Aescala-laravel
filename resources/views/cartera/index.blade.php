@extends('layouts.app')

@section('content')

<div>
    <form id="formPago" method="POST" action="{{ route('cartera.save') }}">
        @csrf

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 m-2">

            {{-- =========================DATOS DEL PAGO==========================--}}
            <div class="border border-gray-200 rounded-lg p-4 bg-white">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">
                    Información del Pago
                </h2>
                <div class="flex flex-wrap">
                    <div class="w-full p-2">
                        <x-input-label for="id_proyecto" :value="__('Seleccione Proyecto *')" />

                        <x-select-input
                            placeholder="Busqueda.."
                            autocomplete="off"
                            name="id_proyecto"
                            id="id_proyecto"
                            :options="$proyectos"
                            :data="['id', 'nombre_proyecto']"
                            :selected="old('id_proyecto', $id_proyecto)"
                            class="block mt-1 w-full"
                        />
                        <x-input-error :messages="$errors->get('id_proyecto')" class="mt-2" />
                    </div>

                    <div class="w-full md:w-6/12 p-2">
                        <x-input-label for="fecha_pago" :value="__('Fecha de Pago *')" />
                        <div class="relative w-full mt-1">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                </svg>
                            </div>
                            <input
                                datepicker
                                datepicker-format="yyyy-mm-dd"
                                autocomplete="off"
                                type="text"
                                id="fecha_pago"
                                name="fecha_pago"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                                placeholder="Seleccione fecha"
                                value="{{ now()->format('Y-m-d') }}"
                                required
                            />
                        </div>
                    </div>
                    <div class="w-full p-2">
                        <x-input-label for="comentario" :value="__('Comentario')" />

                        <textarea
                            id="comentario"
                            name="comentario"
                            class="block w-full mt-1 h-24 rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                            placeholder="Agrega una observación si lo deseas..."
                        ></textarea>
                    </div>

                </div>
            </div>

            {{-- =========================REFERENCIAS==========================--}}
            <div class="border border-gray-200 rounded-lg p-4 bg-white">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">
                        Referencias del Pago
                    </h2>
                    <button
                        type="button"
                        id="btnAddReferencia"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                        + Añadir Referencia
                    </button>
                </div>

                <div class="mb-4 p-3 rounded-lg bg-gray-100 border">
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-700">
                            Total Referencias
                        </span>
                        <span
                            id="totalReferencias"
                            class="text-lg font-bold text-green-700">
                            $0
                        </span>
                    </div>
                </div>
                <div id="contenedorReferencias" class="space-y-4"></div>
            </div>
        </div>

        <div class="flex justify-between p-2">
            <button
                form="formPago"
                type="submit"
                class="bg-green-600 text-white px-6 py-2.5 rounded-lg shadow hover:bg-green-700 focus:ring-4 focus:ring-green-300 transition-all">
                Guardar Pago
            </button>

            <div id="idDivBotones">

            </div>
        </div>
    </form>
</div>

<div id="divCartera"></div>

@include('documentoModal')
@endsection
@section('scripts')



<script src="{{ asset('js/cartera/index.js') }}?v={{ filemtime(public_path('js/cartera/index.js')) }}"></script>

@endsection
