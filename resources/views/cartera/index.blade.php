@extends('layouts.app')
@section('content')

<div>

    @include('cartera.filter')

    <div class="relative overflow-x-auto rounded-lg border border-gray-200 mb-4">
        <h3 class="text-xl font-medium leading-6 text-gray-900 p-4">Cartera de Proyectos</h3>
        <table class="w-full text-left text-sm text-gray-500">
            <x-table-header :headers="$headers_1" />
            <tbody>
                @forelse($items_1 as $item)
                    <tr>
                        <td class="py-2 max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent break-words whitespace-normal">
                            {{ strtolower($item->nombre_proyecto) }}
                        </td>
                        <td class="py-2 max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent break-words whitespace-normal">
                            {{ strtolower($item->nombre_cliente) }} 
                        </td>
                        <td class="py-2 max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent break-words whitespace-normal">
                            {{ strtolower($item->user?->nombre_completo) }}
                        </td>
                        <td class="py-2 max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent break-words whitespace-normal">
                            {!! $item->spanEstado !!}
                        </td>
                        <td class="py-2 max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent break-words whitespace-normal">
                            $ {{ number_format($item->totalPagado, 0, ',', '.') }} / $ {{ number_format($item->total, 0, ',', '.') }}
                        </td>
                        <td class="py-2 max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent break-words whitespace-normal">
                            {!! $item->apaz !!}
                        </td>
                        <td class="py-2 max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent break-words whitespace-normal">
                            <div class=" flex items-center justify-center space-x-2">

                                <a tabindex="0" 
                                    data-tooltip-target="tooltip-hover-pagos-{{$item->id}}" 
                                    data-tooltip-trigger="hover"
                                    x-on:click="$dispatch('open-modal', 'modalPagos-modal')" 
                                    x-data=""
                                    onclick="mostrarPagos({{ $item->id }}, '{{ $item->nombre_proyecto }}', '{{ $item->paz_salvo }}')"
                                    class="flex items-center justify-center w-10 h-10 text-white bg-blue-700 hover:bg-white hover:text-blue-800 border-2 border-blue-800 focus:ring-4 
                                        focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M10.7367 14.5876c.895.2365 2.8528.754 3.1643-.4966.3179-1.2781-1.5795-1.7039-2.5053-1.9117-.1034-.0232-.1947-.0437-.2694-.0623l-.6025 2.4153c.0611.0152.1328.0341.2129.0553Zm.8452-3.5291c.7468.1993 2.3746.6335 2.6581-.5025.2899-1.16213-1.2929-1.5124-2.066-1.68348-.0869-.01923-.1635-.03619-.2262-.0518l-.5462 2.19058c.0517.0129.1123.0291.1803.0472Z"/>
                                        <path fill="currentColor" fill-rule="evenodd" d="M9.57909 21.7008c5.35781 1.3356 10.78401-1.9244 12.11971-7.2816 1.3356-5.35745-1.9247-10.78433-7.2822-12.11995C9.06034.963624 3.6344 4.22425 2.2994 9.58206.963461 14.9389 4.22377 20.3652 9.57909 21.7008ZM14.2085 8.0526c1.3853.47719 2.3984 1.1925 2.1997 2.5231-.1441.9741-.6844 1.4456-1.4013 1.6116.9844.5128 1.485 1.2987 1.0078 2.6612-.5915 1.6919-1.9987 1.8347-3.8697 1.4807l-.454 1.8196-1.0972-.2734.4481-1.7953c-.2844-.0706-.575-.1456-.8741-.2269l-.44996 1.8038-1.09594-.2735.45407-1.8234c-.10059-.0258-.20185-.0522-.30385-.0788-.15753-.0411-.3168-.0827-.47803-.1231l-1.42812-.3559.54468-1.2563s.80844.215.7975.1991c.31063.0769.44844-.1256.50282-.2606l.71781-2.8766.11562.0288c-.04375-.0175-.08343-.0288-.11406-.0366l.51188-2.05344c.01375-.23312-.06688-.52719-.51125-.63812.01718-.01157-.79688-.19813-.79688-.19813l.29188-1.17187 1.51313.37781-.0013.00562c.2275.05657.4619.11032.7007.16469l.4497-1.80187 1.0965.27343-.4406 1.76657c.2944.06718.5906.135.8787.20687l.4375-1.755 1.0975.27344-.4493 1.8025Z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                                <div id="tooltip-hover-pagos-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                    Pagos del Proyecto
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>

                                @if ($item->totalPagado > 0)
                                    <a tabindex="0" 
                                        href="{{ route('cartera.reciboPDF', ['id' => $item->id, 'tipo' => 1]) }}"
                                        data-tooltip-target="tooltip-hover-Recibo-{{$item->id}}" 
                                        data-tooltip-trigger="hover" target="_blank"
                                        class="flex items-center justify-center w-10 h-10 text-white bg-yellow-700 hover:bg-white hover:text-yellow-800 border-2 border-yellow-800 focus:ring-4 
                                            focus:outline-none focus:ring-yellow-300 font-medium rounded-full text-sm dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800">
                                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v-5h1.5a1.5 1.5 0 1 1 0 3H5m12 2v-5h2m-2 3h2M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m6 4v5h1.375A1.627 1.627 0 0 0 14 15.375v-1.75A1.627 1.627 0 0 0 12.375 12H11Z"/>
                                        </svg>
                                    </a>
                                    <div id="tooltip-hover-Recibo-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                        Recibo PDF
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                @endif

                                @if ($item->paz_salvo == 1)
                                    <!-- Botón certificadoPZPDF -->
                                    <div class="relative">
                                        <a tabindex="0"
                                        data-tooltip-target="certificadoPZPDF{{$item->id}}"
                                        data-tooltip-trigger="hover"
                                        href="{{ route('cartera.certificadoPZPDF', ['id' => $item->id, 'tipo' => 1]) }}"
                                        target="_blank"
                                        class="flex items-center justify-center w-10 h-10 text-white bg-slate-700 hover:bg-white hover:text-slate-800 border-2 border-slate-800 focus:ring-4
                                            focus:outline-none focus:ring-slate-300 font-medium rounded-full text-sm dark:bg-slate-600 dark:hover:bg-slate-700 dark:focus:ring-slate-800">
                                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7h1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h11.5M7 14h6m-6 3h6m0-10h.5m-.5 3h.5M7 7h3v3H7V7Z"/>
                                            </svg>
                                        </a>
                                        <div id="certificadoPZPDF{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                            Certificado Paz y Salvo
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers_1) }}" class="py-2 max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent break-words whitespace-normal">
                            No hay registros.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Paginador -->
        @if($items_1->hasPages())
            <div class="mt-4">
                {{ $items_1->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <h3 class="text-xl font-medium leading-6 text-gray-900 p-4">Cartera de Otro Si</h3>
        <table class="w-full text-left text-sm text-gray-500">
            <x-table-header :headers="$headers_2" />
            <tbody>
                @forelse($items_2 as $item)
                    <tr>
                        <td class="py-2 max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent break-words whitespace-normal">
                            Otro si N° {{ $item->numero }} - {{ strtolower($item->proyecto->nombre_proyecto) }}
                        </td>
                        <td class="py-2 max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent break-words whitespace-normal">
                            {{ strtolower($item->proyecto->nombre_cliente) }}
                        </td>

                        <td class="py-2 max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent break-words whitespace-normal">
                            {{ strtolower($item->user_encargado->nombre_completo) }}
                        </td>
                        <td class="py-2 max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent break-words whitespace-normal">
                            {!! $item->spanEstado !!}
                        </td>
                        <td class="py-2 max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent break-words whitespace-normal">
                            $ {{ number_format($item->totalPago, 0, ',', '.') }} / $ {{ number_format($item->totalDeve, 0, ',', '.') }}
                        </td>
                        <td class="py-2 max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent break-words whitespace-normal">
                            {!! $item->apaz !!}
                        </td>
                        <td class="py-2 max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent break-words whitespace-normal">
                            <div class=" flex items-center justify-center space-x-2">

                                <a tabindex="0" 
                                    data-tooltip-target="tooltip-hover-pagos-{{$item->id}}" 
                                    data-tooltip-trigger="hover"
                                    x-on:click="$dispatch('open-modal', 'modalPagos-modal')" 
                                    onclick="mostrarPagosOtroSi({{ $item->id }}, 'Otro si N° {{ $item->numero }} - {{ strtolower($item->proyecto->nombre_proyecto) }}', '{{ $item->paz_salvo }}')"
                                    class="flex items-center justify-center w-10 h-10 text-white bg-blue-700 hover:bg-white hover:text-blue-800 border-2 border-blue-800 focus:ring-4 
                                        focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M10.7367 14.5876c.895.2365 2.8528.754 3.1643-.4966.3179-1.2781-1.5795-1.7039-2.5053-1.9117-.1034-.0232-.1947-.0437-.2694-.0623l-.6025 2.4153c.0611.0152.1328.0341.2129.0553Zm.8452-3.5291c.7468.1993 2.3746.6335 2.6581-.5025.2899-1.16213-1.2929-1.5124-2.066-1.68348-.0869-.01923-.1635-.03619-.2262-.0518l-.5462 2.19058c.0517.0129.1123.0291.1803.0472Z"/>
                                        <path fill="currentColor" fill-rule="evenodd" d="M9.57909 21.7008c5.35781 1.3356 10.78401-1.9244 12.11971-7.2816 1.3356-5.35745-1.9247-10.78433-7.2822-12.11995C9.06034.963624 3.6344 4.22425 2.2994 9.58206.963461 14.9389 4.22377 20.3652 9.57909 21.7008ZM14.2085 8.0526c1.3853.47719 2.3984 1.1925 2.1997 2.5231-.1441.9741-.6844 1.4456-1.4013 1.6116.9844.5128 1.485 1.2987 1.0078 2.6612-.5915 1.6919-1.9987 1.8347-3.8697 1.4807l-.454 1.8196-1.0972-.2734.4481-1.7953c-.2844-.0706-.575-.1456-.8741-.2269l-.44996 1.8038-1.09594-.2735.45407-1.8234c-.10059-.0258-.20185-.0522-.30385-.0788-.15753-.0411-.3168-.0827-.47803-.1231l-1.42812-.3559.54468-1.2563s.80844.215.7975.1991c.31063.0769.44844-.1256.50282-.2606l.71781-2.8766.11562.0288c-.04375-.0175-.08343-.0288-.11406-.0366l.51188-2.05344c.01375-.23312-.06688-.52719-.51125-.63812.01718-.01157-.79688-.19813-.79688-.19813l.29188-1.17187 1.51313.37781-.0013.00562c.2275.05657.4619.11032.7007.16469l.4497-1.80187 1.0965.27343-.4406 1.76657c.2944.06718.5906.135.8787.20687l.4375-1.755 1.0975.27344-.4493 1.8025Z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                                <div id="tooltip-hover-pagos-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                    Pagos del Proyecto
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>

                                @if ($item->totalPago > 0)
                                    <a tabindex="0" 
                                        href="{{ route('cartera.reciboPDF', ['id' => $item->id, 'tipo' => 2]) }}"
                                        data-tooltip-target="tooltip-hover-Recibo-{{$item->id}}" 
                                        data-tooltip-trigger="hover" target="_blank"
                                        class="flex items-center justify-center w-10 h-10 text-white bg-yellow-700 hover:bg-white hover:text-yellow-800 border-2 border-yellow-800 focus:ring-4 
                                            focus:outline-none focus:ring-yellow-300 font-medium rounded-full text-sm dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800">
                                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v-5h1.5a1.5 1.5 0 1 1 0 3H5m12 2v-5h2m-2 3h2M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m6 4v5h1.375A1.627 1.627 0 0 0 14 15.375v-1.75A1.627 1.627 0 0 0 12.375 12H11Z"/>
                                        </svg>
                                    </a>
                                    <div id="tooltip-hover-Recibo-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                        Recibo PDF
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                @endif
                                
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers_2) }}" class="py-2 max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent break-words whitespace-normal">
                            No hay registros.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Paginador -->
        @if($items_2->hasPages())
            <div class="mt-4">
                {{ $items_2->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
    @include('cartera.modalPagos')
@endsection

@section('scripts')
    <script src="{{ asset('js/cartera/index.js') }}?v={{ filemtime(public_path('js/cartera/index.js')) }}"></script>
@endsection
