@extends('layouts.app')
@section('content')

    <div class="flex justify-between mb-3">
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
        <div class="flex flex-wrap justify-end text-end gap-2 ml-2">
            <button type="button" onclick="window.location.href='{{ url('insumos/history') }}'"
                class="w-[160px] bg-red-500 text-white rounded-lg px-4 py-2">
                Historial Entregas
            </button>

            <x-primary-button class="w-[160px] justify-center" x-on:click="$dispatch('open-modal', 'modalEntregaInsumo-modal')" x-data="">
                Entrega Insumo
            </x-primary-button>

            <x-secondary-button class="w-[160px] justify-center" href="{{ route('insumos.create')}}">
                Crear Insumos
            </x-secondary-button>
        </div>

    </div>

    @include('insumos.filter')

    <x-ui.table
        :data="$items"
        :columns="$columns"
        :headers="$headers"

        tableClass="border border-gray-200 rounded-lg"
        rowClass="h-[50px]"

        :columnClass="[]"

        :customCells="[

            'estado' => fn($item) => $item->spanEstado,

            'acciones' => fn($item) => view('insumos.partials.actions', compact('item'))->render(),
        ]"
    />

    @if($items->hasPages())
        {{ $items->links() }}
    @endif

    @include('insumos.modalEntregaInsumo')
    @include('insumos.modalHistoryInsumo')
@endsection

@section('scripts')
    <script src="{{ asset('js/insumos/index.js') }}?v={{ filemtime(public_path('js/insumos/index.js')) }}"></script>
@endsection
