@extends('layouts.app')
@section('content')

    
    <div class="flex justify-end text-center mb-3">
        <x-secondary-button class="ms-4" href="{{ route('user.create')}}">
            Crear Usuario
        </x-secondary-button>
    </div>

    @include('user.filter')

    <x-ui.table
        :data="$items"
        :columns="$columns"
        :headers="$headers"

        tableClass="border border-gray-200 rounded-lg"
        rowClass="h-[50px]"
        
        :columnClass="[
            'nombre'    => 'text-center',
            'documento' => 'text-center',
            'email'     => 'text-center break-all',
            'telefono'  => 'text-center',
            'perfil'    => 'text-center',
            'estado'    => 'text-center',
            'acciones'  => 'text-center',
        ]"

        :customCells="[
            'nombre' => fn($item) => e($item->nombre_completo),

            'documento' => fn($item) =>
                e($item->tipoDOc[0]) . ': ' . e($item->cedula),

            'perfil' => fn($item) =>
                $item->spanRol,

            'estado' => fn($item) =>
                $item->spanEstado,

            'acciones' => fn($item) => view('user.partials.actions', compact('item'))->render(),
        ]"
    />

    @if($items->hasPages())
        {{ $items->links() }}
    @endif

@endsection

@section('scripts')
    <script src="{{ asset('js/user/index.js') }}?v={{ filemtime(public_path('js/user/index.js')) }}"></script>
@endsection