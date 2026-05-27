@extends('layouts.app')
@section('content')
<div class="w-full px-2">
    <form method="POST" action="{{ route('otro_si.save') }}" id="formOtroSi">
        @csrf
        <div class="flex flex-wrap -mx-3">

            <input type="hidden" name="id_user_encargado" id="id_user_encargado" value="{{ old('id_user_encargado', Auth::user()->id) }}">
            <input type="hidden" name="id" id="id" value="{{ old('id', $item->id ?? null) }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                {{-- Select Proyecto --}}
                <div class="md:col-span-2">
                    <x-input-label for="id_proyecto" :value="__('Proyecto *')" />
                    <x-select-input
                        name="id_proyecto"
                        id="id_proyecto"
                        :options="$proyectos"
                        :data="['id', 'nombre_proyecto']"
                        :selected="old('id_proyecto', $item->id_proyecto ?? '')"
                        :class="'block mt-1 w-full ' . ($item ? 'bg-gray-100 cursor-not-allowed' : '')"
                        :disabled="(bool)$item"
                        required
                    />
                    @if($item)
                        <input
                            type="hidden"
                            name="id_proyecto"
                            value="{{ old('id_proyecto', $item->id_proyecto) }}"
                        >
                    @endif
                    <x-input-error :messages="$errors->get('id_proyecto')" class="mt-2" />
                </div>

                {{-- Botón --}}
                <div class="flex justify-end w-full">
                    <x-secondary-button class="flex items-center gap-2 px-4 py-2" id="btn-open-modal"
                        x-on:click="$dispatch('open-modal', 'modalItemsArea-modal')"
                        x-data=""
                    >
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5v14m8-7h-2m0 0h-2m2 0v2m0-2v-2M3 11h6m-6 4h6m11 4H4c-.55228 0-1-.4477-1-1V6c0-.55228.44772-1 1-1h16c.5523 0 1 .44772 1 1v12c0 .5523-.4477 1-1 1Z"/>
                        </svg>
                        <span>Agregar Área</span>
                    </x-secondary-button>
                </div>
            </div>

            <hr class="w-full my-2">
            <div class="mx-auto px-2 py-2 justify-start w-full">
                <h2 class="text-xl font-bold text-[#242e68]">Areas Agregadas</h2>
                <div class="flex w-full items-center space-x-2">
                    <div id="area-div" class="mt-2 space-y-2 w-full">


                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-secondary-button class="ms-4" href="{{ route('otro_si.index') }}">
                Atras
            </x-secondary-button>
            <x-primary-button class="ms-4">
                Guardar
            </x-primary-button>
        </div>
    </form>
</div>
@include('otrosi.modalItemsArea')
@endsection

@section('scripts')
    <script>
        const unidades = @json($unidades);
        const entregablesFromDB = @json($entregables);
    </script>
    <script src="{{ asset('js/otro_si/create.js') }}?v={{ filemtime(public_path('js/otro_si/create.js')) }}"></script>
@endsection

