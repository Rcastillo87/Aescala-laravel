@extends('layouts.app')
@section('content')
    @include('solicitud.filter')
    <div class="flex justify-end text-center mb-3">
        <x-secondary-button class="ms-4" href="{{ route('solicitud.create')}}">
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
                            {{ $item->proyecto->nombre_proyecto }}
                        </td>
                        @if (Auth::User()->isAdmin)
                            <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                                {{ $item->usuario->nombre_completo }}
                            </td>
                        @endif
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item->fecha_solicitud }}
                        </td>
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item->totalItemsEntregado }} / {{ $item->totalItemsSolicitud }}
                        </td>

                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {!! $item->spanEstado !!}
                        </td>
                        <td class="py-2 bg-transparent border-b dark:border-white/40 shadow-transparent flex items-center justify-center">

                            <a tabindex="0"
                                data-tooltip-target="tooltip-listaItems-{{$item->id}}"
                                data-tooltip-trigger="hover" 
                                x-data=""
                                x-on:click="cargarItemsSolicitud({{ $item->id }}); $dispatch('open-modal', 'modalSolicitudLista-modal')"
                                class="flex items-center justify-center w-10 h-10 text-white bg-violet-700 hover:bg-white hover:text-violet-800 border-2 border-violet-800 focus:ring-4 
                                        focus:outline-none focus:ring-violet-300 font-medium rounded-full text-sm dark:bg-violet-600 dark:hover:bg-violet-700 dark:focus:ring-violet-800 cursor-pointer me-2">
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 17h6m-3 3v-6M4.857 4h4.286c.473 0 .857.384.857.857v4.286a.857.857 0 0 1-.857.857H4.857A.857.857 0 0 1 4 9.143V4.857C4 4.384 4.384 4 4.857 4Zm10 0h4.286c.473 0 .857.384.857.857v4.286a.857.857 0 0 1-.857.857h-4.286A.857.857 0 0 1 14 9.143V4.857c0-.473.384-.857.857-.857Zm-10 10h4.286c.473 0 .857.384.857.857v4.286a.857.857 0 0 1-.857.857H4.857A.857.857 0 0 1 4 19.143v-4.286c0-.473.384-.857.857-.857Z"/>
                                </svg>
                            </a>
                            <div id="tooltip-listaItems-{{$item->id}}"
                                role="tooltip"
                                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                Items de la Solicitud
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
                {{ $items->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
    @include('solicitud.modalSolicitudLista')
@endsection

@section('scripts')
    <script src="{{asset('js/solicitud/index.js')}}"></script>
@endsection