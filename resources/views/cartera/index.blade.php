@extends('layouts.app')
@section('content')

    <div class="relative overflow-x-auto rounded-lg border border-gray-200 mb-4">
        <h3 class="text-xl font-medium leading-6 text-gray-900 p-4">Cartera de Proyectos</h3>
        <table class="w-full text-left text-sm text-gray-500">
            <x-table-header :headers="$headers_1" />
            <tbody>
                @forelse($items_1 as $item)
                    <tr>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ strtolower($item->nombre_proyecto) }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ strtolower($item->nombre_cliente) }} 
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ strtolower($item->user?->nombre_completo) }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {!! $item->spanEstado !!}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            $ {{ number_format($item->totalPagado, 0, ',', '.') }} / $ {{ number_format($item->total, 0, ',', '.') }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {!! $item->apaz !!}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            <div class=" flex items-center justify-center space-x-2">

                                <a tabindex="0" 
                                    data-tooltip-target="tooltip-hover-pagos-{{$item->id}}" 
                                    data-tooltip-trigger="hover"
                                    x-on:click="$dispatch('open-modal', 'modalPagos-modal')" 
                                    x-data=""
                                    onclick="mostrarPagos({{ $item->id }}, '{{ $item->nombre_proyecto }}')"
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

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers_1) }}" class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
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
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            Otro si N° {{ $item->numero }} - {{ strtolower($item->proyecto->nombre_proyecto) }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ strtolower($item->proyecto->nombre_cliente) }}
                        </td>

                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ strtolower($item->user_encargado->nombre_completo) }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {!! $item->spanEstado !!}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            $ {{ number_format($item->totalPago, 0, ',', '.') }} / $ {{ number_format($item->totalDeve, 0, ',', '.') }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {!! $item->spanEstado !!}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            <div class=" flex items-center justify-center space-x-2">

                                <a tabindex="0" 
                                    data-tooltip-target="tooltip-hover-pagos-{{$item->id}}" 
                                    data-tooltip-trigger="hover"
                                    x-on:click="$dispatch('open-modal', 'modalPagos-modal')" 
                                    x-data=""
                                    onclick="mostrarPagos({{ $item->id }}, '{{ $item->nombre_proyecto }}')"
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

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers_2) }}" class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
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
    @include('cartera.modalPagos')
    @include('cartera.modalFormPago')
@endsection

@section('scripts')
    <script src="{{asset('js/cartera/index.js')}}"></script>
@endsection
