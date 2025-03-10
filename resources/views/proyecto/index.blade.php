@extends('layouts.app')
@section('content')

<div class="flex flex-wrap md:flex-nowrap justify-between text-center border-2 p-3 rounded-lg border-gray-200 mb-3">
    <!-- Sección de etiquetas -->
    <div class="flex flex-wrap gap-4 md:gap-2">
        <span class="flex items-center text-center">
            Normal (80%)
            <hr class="border-2 bg-green-500 rounded-lg w-[55px] p-[3px] ml-1">
        </span>
        <span class="flex items-center text-center ml-2">
            Próximos a vencer (95%)
            <hr class="border-2 bg-orange-400 rounded-lg w-[55px] p-[3px] ml-1">
        </span>

        <span class="flex items-center text-center ml-2">
            Atrasados (>> 100%)
            <hr class="border-2 bg-red-500 rounded-lg w-[55px] p-[3px] ml-1">
        </span>
    </div>

    <!-- Botón responsive -->
    <x-secondary-button class="mt-4 md:mt-0" href="{{ route('proyecto.create')}}">
        Crear Proyectos
    </x-secondary-button>
</div>
    @forelse ($items as $item)
        @php
            $porcen = $item->dias_procentage;
            $bg = match (true) {
                $porcen <= 80 => 'bg-green-500',
                $porcen <= 95 => 'bg-orange-400',
                default => 'bg-red-500',
            };
        @endphp
        <div class="flex border-2 rounded-lg pb-1 border-gray-300 shadow-lg shadow-black-200 mb-2">
            <div class="text-center items-center w-[130px] h-[110px] border-2 rounded-xl {{ $bg }} mb-1 ml-3 mt-2 flex flex-col justify-center">
                <p class="text-white text-4xl font-bold">{{$item->dias_transcurridos}}</p>
                <span class="text-white text-xl font-bold">{{$item->dias_procentage}}%</span>
                <small class="text-white">F In: {{$item->fecIni}}</small>
                <small class="text-white">F Es: {{$item->fec_fin_est}}</small>
            </div>
            <div class="flex flex-wrap w-full">
                <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1">
                    <p class="text-lg text-gray-500 font-bold">Nombre Proyecto</p>
                    <span class="text-md text-black">{{$item->nombre_proyecto}}</span>
                </div>
                <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                    <p class="text-lg text-gray-500 font-bold">Ubicacion</p>
                    <span class="text-md text-black">{{$departamentos[intval($item['departamento'])]['departamento']}} - 
                        {{$departamentos[intval($item['departamento'])]['ciudades'][$item['ciudad']]}}</span>
                </div>
                <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                    <p class="text-lg text-gray-500 font-bold">Dirrecion</p>
                    <span class="text-md text-black">{{$item->direccion}}</span>
                </div>
                <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                    <p class="text-lg text-gray-500 font-bold">Contacto Cliente</p>
                    <span class="text-md text-black">{{$item->nombre_cliente}}</span>
                </div>
                <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                    <p class="text-lg text-gray-500 font-bold">Telefono Cliente</p>
                    <span class="text-md text-black">{{$item->telefono_cliente}}</span>
                </div>
                <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                    <p class="text-lg text-gray-500 font-bold">Colaborador Encargado</p>
                    <span class="text-md text-black">{{$item->user->nombre_completo}}</span>
                </div>
                <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                    <p class="text-lg text-gray-500 font-bold">Total Proyecto</p>
                    <span class="text-md text-black">{{number_format($item->totalProyecto)}}$</span>
                </div>
                <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                    <p class="text-lg text-gray-500 font-bold">Total Valance</p>
                    <span class="text-md text-black">{{number_format(1000)}}$</span>
                </div>
            </div>
            <div class="flex flex-col text-center w-[100px] border-l-2 px-2 mx-2 mt-1">
                <p class="flex text-gray-500 text-lg font-bold mx-2">Opciones</p>
                <div class="flex items-center p-1 text-center">
                    <a data-tooltip-target="tooltip-hover-edit-{{$item->id}}" data-tooltip-trigger="hover" href="{{ route('proyecto.edit', $item->id) }}"
                        class="flex items-center justify-center w-10 h-10 text-white bg-green-700 hover:bg-white hover:text-green-800 border-2 border-green-800 focus:ring-4 
                               focus:outline-none focus:ring-green-300 font-medium rounded-full text-sm dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800 me-2">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                        </svg>
                    </a>
                    <div id="tooltip-hover-edit-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                        Editar
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>


                </div>
            </div>
        </div>
    @empty
        <div class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
            No hay registros.
        </div>
    @endforelse
    <!-- Paginador -->
    @if($items->hasPages())
        <div class="mt-4">
            {{ $items->links() }}
        </div>
    @endif

    <script src="{{asset('js/protecto/index.js')}}"></script>

@endsection

