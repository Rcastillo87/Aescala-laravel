@extends('layouts.app')

@section('content')

<div class="w-full space-y-2">
    <div class="bg-white shadow-sm rounded-2xl border border-gray-200 px-4 py-2">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            @if($id_proyecto)
                <div class="min-w-0">
                    <p class="text-sm text-gray-500">
                        Proyecto
                    </p>
                    <h1 class="text-lg sm:text-xl font-semibold text-gray-800 break-words">
                        {{ $proyecto?->nombre_proyecto??'--' }}
                    </h1>
                </div>
                <div class="w-full md:w-auto">
                    <button
                        x-data=""
                        x-on:click="$dispatch('open-modal', 'modalPagos-modal')"
                        type="button"
                        class="w-full md:w-auto inline-flex items-center justify-center gap-2
                            bg-blue-600 hover:bg-blue-700
                            text-white font-medium
                            px-5 py-3
                            rounded-xl
                            shadow-sm
                            transition-all duration-200">

                        <span class="text-lg leading-none">+</span>
                        <span>
                            Añadir Abono
                        </span>
                    </button>
                </div>
            @else
                <div class="flex flex-wrap w-full">
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
                </div>
            @endif
        </div>
    </div>
    <div id="idDivBotones"></div>
    <div id="divCartera" class="bg-white rounded-2xl border border-gray-200 shadow-sm px-4 py-2 overflow-x-auto"></div>
</div>

@include('documentoModal')
@include('cartera.modalRC')
@include('cartera.modalPagos')
@include('cartera.modalOtroSiRefe')
@endsection

@section('scripts')
    <script src="{{ asset('js/cartera/index.js') }}?v={{ filemtime(public_path('js/cartera/index.js')) }}"></script>
@endsection
