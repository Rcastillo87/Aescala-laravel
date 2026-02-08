@extends('layouts.app')
@section('content')

    @include('pedidos.filter')
    <div class="flex justify-end text-center mb-3">
        <x-secondary-button class="ms-4" href="{{ route('pedidos.create')}}">
            Crear Pedido
        </x-secondary-button>
    </div>
    <div class="relative overflow-x-auto rounded-lg border border-gray-200">
        <table class="w-full text-left text-sm text-gray-500">
            <x-table-header :headers="$headers" />
            <tbody>
                @forelse($items as $item)
                    <tr class="h-10">
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item['factura'] }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ strtolower($item['proveedor']) }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item['fecha'] }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item['items'] }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            ${{ number_format($item['total']) }}
                        </td>
                        <td class="py-2 truncate max-w-xs bg-transparent border-b dark:border-white/40 shadow-transparent flex items-center justify-center">
                            <a tabindex="0" 
                            data-tooltip-target="tooltip-hover-{{$item['factura']}}" 
                            data-tooltip-trigger="hover" x-data="" 
                            x-on:click="$dispatch('open-modal', 'factura-modal')" 
                            onclick="openPedidos(0, {{ $item['factura'] }})" 
                            class="flex items-center justify-center w-10 h-10 text-white bg-violet-700 hover:bg-white hover:text-violet-800 border-2 border-violet-800 focus:ring-4 
                                      focus:outline-none focus:ring-violet-300 font-medium rounded-full text-sm dark:bg-violet-600 dark:hover:bg-violet-700 dark:focus:ring-violet-800 cursor-pointer">
                                <svg class="class='w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 4h3a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h3m0 3h6m-3 5h3m-6 0h.01M12 16h3m-6 0h.01M10 3v4h4V3h-4Z"></path>
                                </svg>
                            </a>
                            <div id="tooltip-hover-{{$item['factura']}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                Cambio de Estado
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
                {{ $items->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
    @include('pedidos.modalPedidos')
@endsection

@section('scripts')
    <script src="{{ asset('js/pedidos/index.js') }}?v={{ filemtime(public_path('js/pedidos/index.js')) }}"></script>
@endsection