@extends('layouts.app')
@section('content')
<div class="w-full px-2">
    <form method="POST" action="{{ route('otro_si.save') }}" id="formOtroSi"> 
        @csrf
        <div class="flex flex-wrap -mx-3">

            <input type="hidden" name="id_user_encar" id="id_user_encar" value="{{ Auth::user()->id }}">
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-6/12 2xl:w-4/12 md:flex-0">
                <x-input-label for="id_proyecto" :value="__('Proyecto *')" />
                <x-select-input name="id_proyecto" id="id_proyecto" :options="$proyectos" :data="['id', 'nombre_proyecto']"
                    :selected="old('id_proyecto')" class="block mt-1 w-full" required />
                <x-input-error :messages="$errors->get('id_proyecto')" class="mt-2" />
            </div>


            <hr class="w-full my-2">
            <div class="mx-auto px-2 py-2 justify-start w-full">
                <h2 class="text-xl font-bold text-[#242e68]">Entregables</h2>
                <div class="flex w-full items-center space-x-2">
                    <div class="py-1 shrink-0">
                        <x-secondary-button class="my-2 py-1 px-1" href="#" data-tooltip-target="tooltip-hover-Entregable" data-tooltip-trigger="hover" 
                            x-data="" x-on:click="$dispatch('open-modal', 'modalOtrosi-modal')">
                            <svg class="w-6 h-6 text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
                            </svg>
                        </x-secondary-button>
                        <div id="tooltip-hover-Entregable" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                            Añadir Entregable
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </div>

                    <div id="entregables-div" class="mt-2 space-y-2 w-full">
                        {!! $entregableProye??'' !!}
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
@include('otrosi.modalOtrosi')
@endsection

@section('scripts')
    <script>
        const saveUrl = "{{ route('otro_si.save') }}";
    </script>
    <script src="{{ asset('js/otro_si/create.js') }}"></script>

@endsection