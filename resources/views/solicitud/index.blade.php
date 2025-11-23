@extends('layouts.app')
@section('content')

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
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item->usuario->nombre_completo }}
                        </td>
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
@endsection

@section('scripts')
    <script src="{{asset('js/solicitud/index.js')}}"></script>
@endsection