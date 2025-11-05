@extends('layouts.app')
@section('content')

    <div class="overflow-x-auto rounded-lg border border-gray-200">
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
                            $ {{ number_format(0, 0, ',', '.') }} / $ {{ number_format($item->total, 0, ',', '.') }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            <div class=" flex items-center justify-center space-x-2">


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
                            {{ strtolower($item->proyecto->nombre_proyecto) }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            N° {{ $item->numero }}
                        </td>

                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ strtolower($item->user_encargado->nombre_completo) }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {!! $item->spanEstado !!}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            <div class=" flex items-center justify-center space-x-2">


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




@endsection

@section('scripts')
    <script src="{{asset('js/comercial/index.js')}}"></script>
@endsection
