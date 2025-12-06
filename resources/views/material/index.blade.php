@extends('layouts.app')
@section('content')

    @include('material.filter')
    <div class="flex justify-between  mb-3">

        <div class="flex flex-wrap md:flex-nowrap justify-start text-center border-2 p-3 bg-gray-100 rounded-lg border-gray-200">
            <!-- Sección de etiquetas -->
            <div class="flex flex-wrap gap-4 md:gap-2">
                <span class="flex items-center text-center ml-2">
                    Sin inventario(stock = 0)
                    <hr class="border-2 bg-red-200 rounded-lg w-[55px] p-[3px] ml-1">
                </span>
                <span class="flex items-center text-center">
                    Poco invertario (stock &lt;= stock minimo)
                    <hr class="border-2 bg-orange-200 rounded-lg w-[55px] p-[3px] ml-1">
                </span>
                <span class="flex items-center text-center ml-2">
                    Inventario suficiente (stock &gt; stock minimo)
                    <hr class="border-2 bg-white rounded-lg w-[55px] p-[3px] ml-1">
                </span>
            </div>
        </div>

        <x-secondary-button class="ms-4" href="{{ route('material.create')}}">
            Crear Material
        </x-secondary-button>
    </div>
    <div class="relative overflow-x-auto rounded-lg border border-gray-200">
        <table class="w-full text-left text-sm text-gray-500">
            <x-table-header :headers="$headers" />
            <tbody>
                @forelse($items as $item)
                    @php
                        $bg = '';
                        if($item->cantidad == 0){
                            $bg = 'bg-red-100';
                        }
                        if(($item->cantidad <= $item->cantidad_min) && ($item->cantidad > 0)){
                            $bg = 'bg-orange-200';
                        }
                    @endphp
                    <tr class="{{ $bg }}">
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item->nombre_material }}
                        </td>
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item->cantidad}} {{ $item->unidades }}
                        </td>
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item->cantidad_min }} {{ $item->unidades }}
                        </td>
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            ${{ number_format($item->valor_unidad) }}
                        </td>
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ explode(' ',  $item->createdAt)[0] }}
                        </td>
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {!! $item->tipoMaterial !!}
                        </td>
                        <td class="py-2 text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {!! $item->spanEstado !!} {!! $item->spanAprobar !!}
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

                            <a data-tooltip-target="tooltip-hover-edit-{{$item->id}}" data-tooltip-trigger="hover" href="{{ route('material.edit', $item->id) }}"
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
                {{ $items->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="{{asset('js/material/index.js')}}"></script>
@endsection
