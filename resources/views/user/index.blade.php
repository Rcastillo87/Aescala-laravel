@extends('layouts.app')
@section('content')

    @include('user.filter')
    <div class="flex justify-end text-center mb-3">
        <x-secondary-button class="ms-4" href="{{ route('user.create')}}">
            Crear Usuario
        </x-secondary-button>
    </div>
    <div class="relative overflow-x-auto rounded-lg border border-gray-200">
        <table class="w-full text-left text-sm text-gray-500">
            <x-table-header :headers="$headers" />
            <tbody>
                @forelse($items as $item)
                    <tr class="h-[50px]">
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item->nombre_completo }}
                        </td>
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item->tipoDOc[0] }}: {{ $item->cedula }}
                        </td>
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item->email }}
                        </td>
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item->telefono }}
                        </td>
                        <td class="items-center py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">

                            @php
                                $proyectos = $item->progresProyecto;
                                $btProyecto = $proyectos->isEmpty() ? 'hidden' : '';
                                $tareas = $item->progresTarea;
                                $btTarea = $tareas->isEmpty() ? 'hidden' : '';
                            @endphp

                            <button id="proyecto-dropdownHoverButton{{ $item->id }}" data-dropdown-toggle="proyecto-dropdownHover{{ $item->id }}" data-dropdown-trigger="hover" 
                                class="my-1 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-2 py-1 
                                text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 {{ $btProyecto }}" type="button">
                                Proyectos 
                                <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                </svg>
                            </button>
                            <div id="proyecto-dropdownHover{{ $item->id }}" class="z-10 hidden border-2 border-gray-400  bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-700">
                                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="proyecto-dropdownHoverButton{{ $item->id }}">
                                    @foreach ($proyectos as $proyecto)
                                        <li>{{$proyecto->nombre_proyecto}}</li>
                                    @endforeach
                                </ul>
                            </div>

                            <button id="tarea-dropdownHoverButton{{ $item->id }}" data-dropdown-toggle="tarea-dropdownHover{{ $item->id }}" data-dropdown-trigger="hover" 
                                class="my-1 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-2 py-1 
                                text-center inline-flex items-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 {{ $btTarea }}" type="button">
                                tareas 
                                <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                </svg>
                            </button>
                            <div id="tarea-dropdownHover{{ $item->id }}" class="z-10 hidden border-2 border-gray-400  bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-700">
                                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="tarea-dropdownHoverButton{{ $item->id }}">
                                    @foreach ($tareas as $tarea)
                                    <li>
                                        {{ optional($tarea->proyecto)->nombre_proyecto ?? 'Sin Proyecto' }} 
                                        -> 
                                        {{ optional($tarea->tareaTipo)->nombre_tarea ?? 'Sin Tipo de Tarea' }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>

                        </td>
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {!! $item->spanRol !!}
                        </td>
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {!! $item->spanEstado !!}
                        </td>
                        <td class="py-2 bg-transparent border-b dark:border-white/40 shadow-transparent flex items-center justify-center">
                            <a data-tooltip-target="tooltip-hover-{{$item->id}}" data-tooltip-trigger="hover" 
                                onclick="cambiarEstado({{ $item->id }}, {{$item->activo}})" 
                                class="flex items-center justify-center w-10 h-10 text-white bg-violet-700 hover:bg-white hover:text-violet-800 border-2 border-violet-800 focus:ring-4 
                                    focus:outline-none focus:ring-violet-300 font-medium rounded-full text-sm dark:bg-violet-600 dark:hover:bg-violet-700 dark:focus:ring-violet-800 cursor-pointer me-2">
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 20V7m0 13-4-4m4 4 4-4m4-12v13m0-13 4 4m-4-4-4 4"/>
                                    </svg>                    
                            </a>
                            <div id="tooltip-hover-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                Cambio de Estado
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                            <a data-tooltip-target="tooltip-hover-edit-{{$item->id}}" data-tooltip-trigger="hover" href="{{ route('user.edit', $item->id) }}"
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
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) }}" class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            No hay registros.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Paginador -->
        @if($items->hasPages())
            <div class="mt-4">
                {{ $items->links() }}
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="{{asset('js/user/index.js')}}"></script>
@endsection