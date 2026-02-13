@extends('layouts.app')
@section('content')

    @include('despachos.filter')

    <x-ui.table
        :data="$items"
        :columns="$columns"
        :headers="$headers"

        tableClass="border border-gray-200 rounded-lg"

        rowClass="h-[50px]"

        :customCells="[
            'acciones' => fn($item) => view('despachos.partials.actions', compact('item'))->render(),
        ]"
    />

    @if($items->hasPages())
        {{ $items->links() }}
    @endif

    <div class="flex justify-end mt-6">
        <x-secondary-button class="ms-4" href="{{ route('despachos.index') }}">
            Atras
        </x-secondary-button>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            new TomSelect("#id_material",{
                create: true,
                allowEmptyOption: true,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                onInitialize: function() {
                    this.wrapper.classList.add("tom-select-custom");
                }
            });
        });
    </script>
@endsection