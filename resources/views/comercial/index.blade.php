@extends('layouts.app')
@section('content')
    <div class="flex justify-between text-center mb-3">
        <div>

        </div>

        <x-secondary-button class="ms-4" href="{{ route('material.create')}}">
            Crear Material
        </x-secondary-button>
    </div>
    <div class="relative overflow-x-auto rounded-lg border border-gray-200">
        <table class="w-full text-left text-sm text-gray-500">
            <x-table-header :headers="$header" />
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item->id }}
                        </td>
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item->nombre_proyecto}}
                        </td>
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item->nombre_cliente }}
                        </td>
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            <span class="text-md">{{$departamentos[intval($item['departamento'])]['departamento']}} - 
                                {{$departamentos[intval($item['departamento'])]['ciudades'][$item['ciudad']]}}
                            </span>
                        </td>
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item->direccion }}
                        </td>
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item->telefono_cliente }}
                        </td>
                        <td class="py-2 bg-transparent border-b dark:border-white/40 shadow-transparent flex items-center justify-center">
                            <a tabindex="0" data-tooltip-target="tooltip-hover-{{$item->id}}" data-tooltip-trigger="hover" 
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
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($header) }}" class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            No hay registros.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Paginador -->
        @if($items->hasPages())
            <div class="mt-4">
                {{ $items->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection

@section('scripts')

@endsection

