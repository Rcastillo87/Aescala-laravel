@extends('layouts.app')
@section('content')

    @include('herramienta.filter')
    <div class="flex justify-end text-center mb-3">
        <x-secondary-button class="ms-4" href="{{ route('herramienta.create')}}">
            Crear Herramienta
        </x-secondary-button>
    </div>
    <div class="relative overflow-x-auto rounded-lg border border-gray-200">
        <table class="w-full text-left text-sm text-gray-500">
            <x-table-header :headers="$headers" />
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ strtolower($item->nombre_herramienta) }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ strtolower($item->referencia) }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ strtolower($item->marca) }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item->createdAt }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {!! $item->spanEstado !!}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {!! ( ($item->prestamo) && ($item->prestamo->tipo_prestamo == 2) )?($item->prestamo->spanPrestamo??''):'' !!}
                        </td>
                        @php
                            $user = null;
                            if ( ($item->prestamo) && ($item->prestamo->tipo_prestamo == 2) ) {
                                $user = strtolower($item->prestamo->user->nombre_completo??'');
                            }
                        @endphp
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $user }}
                        </td>
                        <td class="py-2 truncate max-w-xs bg-transparent border-b dark:border-white/40 shadow-transparent flex items-center justify-center">
                            <a tabindex="0" data-tooltip-target="tooltip-hover-edit-{{$item->id}}" data-tooltip-trigger="hover" href="{{ route('herramienta.edit', $item->id) }}"
                                class="flex items-center justify-center w-10 h-10 text-white bg-green-700 hover:bg-white hover:text-green-800 border-2 border-green-800 focus:ring-4 
                                       focus:outline-none focus:ring-green-300 font-medium rounded-full text-sm dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800 me-2">
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                                </svg>
                            </a>
                            <div id="tooltip-hover-edit-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 truncate max-w-xs text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                Editar
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>

                            <button
                                onclick="listPrestamos(0,{{$item->id}})"
                                x-data data-tooltip-target="tooltip-hover-prestamo-{{$item->id}}" data-tooltip-trigger="hover"
                                x-on:click="$dispatch('open-modal', 'my-modal')"
                                class="flex items-center justify-center w-10 h-10 text-white bg-blue-700 hover:bg-white hover:text-blue-800 border-2 border-blue-800 focus:ring-4 
                                       focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 me-2"
                            >
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.651 7.65a7.131 7.131 0 0 0-12.68 3.15M18.001 4v4h-4m-7.652 8.35a7.13 7.13 0 0 0 12.68-3.15M6 20v-4h4"/>
                                </svg>
                            </button>
                            <div id="tooltip-hover-prestamo-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 truncate max-w-xs text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                Prestamo o Devolució
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) }}" class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
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
    @include('herramienta.modalSavePrestamo')
@endsection

@section('scripts')
    <script src="{{asset('js/herramienta/index.js')}}"></script>
@endsection
