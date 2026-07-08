@extends('layouts.app')
@section('content')
    @include('solicitud.filter')

    @if (!Auth::user()->isAnalista)
        <div class="flex justify-end text-center mb-3">
            <x-secondary-button class="ms-4" href="{{ route('solicitud.create')}}">
                Crear Solicitud
            </x-secondary-button>
        </div>
    @endif

    <div class="relative overflow-x-auto rounded-lg border border-gray-200">
        <table class="w-full text-left text-sm text-gray-500">
            <x-table-header :headers="$headers" />
            <tbody>
                @forelse($items as $item)
                    <tr class="h-[50px]">

                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item->proyecto->nombre_proyecto }}
                        </td>

                        @php
                            $depts = json_decode($departamentos, true)
                        @endphp
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{$depts[intval($item->proyecto->departamento)]['departamento']}} -
                            {{$depts[intval($item->proyecto->departamento)]['ciudades'][intval($item->proyecto->ciudad)]}} -
                            {{ $item->proyecto->ubicacion !== null ? ($ubicacion[$item->proyecto->ubicacion] ?? 'N/A') : 'N/A' }}
                        </td>

                        @if (Auth::User()->isAdmin || Auth::user()->isAnalista|| Auth::user()->isAlmacenista)
                            <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                                {{ $item->usuario->nombre_completo }}
                            </td>
                        @endif
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item->fecha_solicitud }}
                        </td>
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            @if($item->estado == 4)
                                0 / {{ $item->cotizacion()->sum('cantidad') }}
                            @else
                                {{ $item->totalItemsEntregado }} / {{ $item->totalItemsSolicitud }}
                            @endif
                        </td>

                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {!! $item->spanEstado !!}
                        </td>

                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {!! $item->estadoItems !!}
                        </td>

                        <td class="py-2 bg-transparent border-b dark:border-white/40 shadow-transparent flex items-center justify-center">

                            @if($item->estado != 4)
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
                            @endif

                            @if($item->estado == 4)
                                <a tabindex="0"
                                    data-tooltip-target="tooltip-cotizacion-{{$item->id}}"
                                    data-tooltip-trigger="hover"
                                    onclick="descargaLink('{{Route('solicitud.PDFCotizacion', $item->id)}}?view=1')"
                                    class="flex items-center justify-center w-10 h-10 text-white bg-yellow-700 hover:bg-white hover:text-yellow-800 border-2 border-yellow-800 focus:ring-4
                                            focus:outline-none focus:ring-yellow-300 font-medium rounded-full text-sm dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800 cursor-pointer me-2">
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 4h3a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h3m0 3h6m-3 5h3m-6 0h.01M12 16h3m-6 0h.01M10 3v4h4V3h-4Z"/>
                                    </svg>
                                </a>
                                <div id="tooltip-cotizacion-{{$item->id}}"
                                    role="tooltip"
                                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                    PDF Factura de Cotizacion
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>

                                <a tabindex="0"
                                    data-tooltip-target="tooltip-cotsol-{{$item->id}}"
                                    data-tooltip-trigger="hover"
                                    onclick="solicitoCotizacion('{{Route('solicitud.solicitarCotizacion', $item->id)}}')"
                                    class="flex items-center justify-center w-10 h-10 text-white bg-gray-700 hover:bg-white hover:text-gray-800 border-2 border-gray-800 focus:ring-4
                                            focus:outline-none focus:ring-gray-300 font-medium rounded-full text-sm dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800 cursor-pointer me-2">
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.651 7.65a7.131 7.131 0 0 0-12.68 3.15M18.001 4v4h-4m-7.652 8.35a7.13 7.13 0 0 0 12.68-3.15M6 20v-4h4"/>
                                    </svg>
                                </a>
                                <div id="tooltip-cotsol-{{$item->id}}"
                                    role="tooltip"
                                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                    Solicitar material cotizado
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>

                            @endif

                            @if((Auth::user()->isAdmin || Auth::user()->isAlmacenista) && ($item->estado == 1 || ($item->estado == 2)) )
                                <a tabindex="0"
                                    data-tooltip-target="tooltip-despachar-{{$item->id}}"
                                    data-tooltip-trigger="hover"
                                    href="{{ route('solicitud.createDespachoSolicitud', $item->id)}}"
                                    class="flex items-center justify-center w-10 h-10 text-white bg-blue-700 hover:bg-white hover:text-blue-800 border-2 border-blue-800 focus:ring-4
                                            focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 cursor-pointer me-2">
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7h-1M8 7h-.688M13 5v4m-2-2h4"/>
                                    </svg>

                                </a>
                                <div id="tooltip-despachar-{{$item->id}}"
                                    role="tooltip"
                                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                    Despachar Solicitud
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            @endif

                            @if ($item->aprobarItems && (Auth::user()->isAdmin || Auth::user()->isAnalista))
                                <a data-tooltip-target="tooltip-hover-aprobar-{{$item->id}}" data-tooltip-trigger="hover" href="{{ route('solicitud.createAprobarSolicitud', $item->id)}}"
                                    class="flex items-center justify-center w-10 h-10 text-white bg-green-700 hover:bg-white hover:text-green-800 border-2 border-green-800 focus:ring-4
                                            focus:outline-none focus:ring-green-300 font-medium rounded-full text-sm dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800 me-2">
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 1 1 0-18c1.052 0 2.062.18 3 .512M7 9.577l3.923 3.923 8.5-8.5M17 14v6m-3-3h6"/>
                                    </svg>
                                </a>
                                <div id="tooltip-hover-aprobar-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                    Aprobar Items
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            @endif

                            @if (($item->estado == 1 || $item->estado == 4) && !Auth::user()->isAnalista)
                                <div class="relative">
                                    <form action="{{ route('solicitud.delete', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <a tabindex="0"
                                           data-tooltip-target="tooltip-hover-delete-{{ $item->id }}"
                                           data-tooltip-trigger="hover"
                                           onclick="confirmDelete(this)"
                                           class="flex items-center justify-center w-10 h-10 text-white bg-red-600 hover:bg-white hover:text-red-600 border-2 border-red-600 focus:ring-4
                                                  focus:outline-none focus:ring-red-300 font-medium rounded-full text-sm dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-500 cursor-pointer">
                                            ✕
                                        </a>
                                    </form>
                                    <div id="tooltip-hover-delete-{{ $item->id }}"
                                         role="tooltip"
                                         class="absolute z-10 inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs tooltip dark:bg-gray-700 opacity-0 invisible">
                                        Eliminar Solicitud de Material
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                </div>
                            @endif

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

    @if(session('despacho_codigo'))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const url = "{{ route('solicitud.pdfDespacho') }}"
                    + "?id={{ session('despacho_id_proyecto') }}"
                    + "&codigo={{ session('despacho_codigo') }}"
                    + "&view=1";
                window.open(url, '_blank');
            });
        </script>
    @endif

    <script>
        window.departamentos = @json(json_decode($departamentos));
        window.selectedDepartamento = "{{ request('departamento') }}";
        window.selectedCiudad = "{{ request('ciudad') }}";
    </script>
    <script src="{{ asset('js/solicitud/index.js') }}?v={{ filemtime(public_path('js/solicitud/index.js')) }}"></script>
@endsection
