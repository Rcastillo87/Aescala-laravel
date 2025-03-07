@extends('layouts.app')
@section('content')

    <div class="flex justify-between text-center border-2 p-3 rounded-lg border-gray-200 mb-3">
        <div class="flex">
            <span class="flex items-center text-center me-3">Normal (80%)<hr class="border-2 border-green-500 rounded-lg bg-green-500 w-20 pt-2 ml-2"></span>
            <span class="flex items-center text-center me-3">Próximos a vencer (95%)<hr class="border-2 border-orange-400 rounded-lg bg-orange-400 w-20 pt-2 ml-2"></span>
            <span class="flex items-center text-center">Atrasados (>> 100%)<hr class="border-2 border-red-500 rounded-lg bg-red-500 w-20 pt-2 ml-2"></span>
        </div>
        <x-secondary-button class="ms-4" href="{{ route('material.create')}}">
            Crear Proyectos
        </x-secondary-button>
    </div>

    @forelse ($items as $item)
        <div class="flex border-2 rounded-lg mb-2 border-gray-300 shadow-lg shadow-black-200">
            <div class="text-center items-center w-[100px] h-[100px] border-2 rounded-3xl bg-red-500 mb-1 ml-3 mt-2">
                <p class="text-white text-6xl font-bold">89</p>
                <span class="text-white text-2xl font-bold">80%</span>
            </div>
            <div class="flex flex-wrap w-full">
                <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1">
                    <p class="text-lg text-gray-500 font-bold">Nombre Proyecto</p>
                    <span class="text-md text-black">{{$item->nombre_proyecto}}</span>
                </div>
                <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                    <p class="text-lg text-gray-500 font-bold">Ubicacion</p>
                    <span class="text-md text-black">{{$item->departamento}} - {{$item->ciudad}}</span>
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
                    <span class="text-md text-black">{{$item->totalProyecto}}</span>
                </div>
                <div class="w-full max-w-full pl-2 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0 mb-1 pt-1">
                    <p class="text-lg text-gray-500 font-bold">Total Valance</p>
                    <span class="text-md text-black">{{1000}}</span>
                </div>
            </div>
            <div class="flex text-center w-[100px] border-l-2 mb-1 mx-2 mt-2">
                <p class="text-gray-500 text-lg font-bold mx-2">Opciones</p>
                <div class="flex items-center text-center">

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

    <script src="{{asset('js/material/index.js')}}"></script>

@endsection

