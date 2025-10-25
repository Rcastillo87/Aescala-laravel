@extends('layouts.app')
@section('content')
<div class="w-full px-2">
    <form method="POST" action="{{ route('otro_si.save') }}" enctype="multipart/form-data" id="formOtroSi"> 
        @csrf

        <div class="flex justify-end">
            @if (!$item)
                <x-secondary-button  href="{{ route('otro_si.plantilla_otrosi') }}">
                    <svg class="w-6 h-6 mr-2 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 15v2a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-2m-8 1V4m0 12-4-4m4 4 4-4"/>
                    </svg>
                    Descargar Plantilla
                </x-secondary-button>
            @else
                <x-secondary-button  href="#" data-plantilla="{{ $item->plantilla }}" id="downloadPlantillaBtn">
                    <svg class="w-6 h-6 mr-2 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 15v2a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-2m-8 1V4m0 12-4-4m4 4 4-4"/>
                    </svg>
                    Descargar Plantilla
                </x-secondary-button>
            @endif
        </div>


        <div class="flex flex-wrap -mx-3">

            <input type="hidden" name="id_user_encargado" id="id_user_encargado" value="{{ old('id_user_encargado', Auth::user()->id) }}">

            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-6/12 2xl:w-4/12 md:flex-0">
                <x-input-label for="id_proyecto" :value="__('Proyecto *')" />
                <x-select-input 
                    name="id_proyecto" 
                    id="id_proyecto" 
                    :options="$proyectos" :data="['id', 'nombre_proyecto']"
                    :selected="old('id_proyecto', $item->id_proyecto ?? '')" 
                    class="block mt-1 w-full" required />
                <x-input-error :messages="$errors->get('id_proyecto')" class="mt-2" />
            </div>

            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-6/12 2xl:w-4/12 md:flex-0">
                <x-file-input
                    id="plantilla_otro_si"
                    name="plantilla_otro_si"
                    label="Cargue de Plantilla"
                    accept=".xlsx, .xls"
                    required
                    :error="$errors->first('plantilla_otro_si')"
                />
            </div>

            <hr class="w-full my-2">
            <div class="mx-auto px-2 py-2 justify-start w-full">
                <h2 class="text-xl font-bold text-[#242e68]">Items del Otro Si</h2>
                <div class="flex w-full items-center space-x-2">
                    <div id="entregables-div" class="mt-2 space-y-2 w-full">

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
    <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
    <script>
        const saveUrl = "{{ route('otro_si.save') }}";
    </script>
    <script src="{{ asset('js/otro_si/create.js') }}"></script>

@endsection