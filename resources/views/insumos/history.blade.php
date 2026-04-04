@extends('layouts.app')
@section('content')

    <x-ui.table
        :data="$items"
        :columns="$columns"
        :headers="$headers"

        tableClass="border border-gray-200 rounded-lg"
        rowClass="h-[50px]"

        :columnClass="[]"

        :customCells="[
            'nombre_insumo' => fn($item) => $item->insumo->nombre_insumo,
            'area_empresa' => fn($item) => $item->area_empresa->nombre_area,
        ]"
    />

    @if($items->hasPages())
        {{ $items->links() }}
    @endif

    <div class="flex items-center justify-end mt-4">
        <x-secondary-button class="ms-4" href="{{ route('insumos.index') }}">
            Atras
        </x-secondary-button>
    </div>

@endsection
