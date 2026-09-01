@extends('layouts.app')
@section('content')

    @if(Auth::user()->isAdmin)
        @include('novedades.filter')
    @endif

    <div class="flex justify-end">
        <button type="button" 
            onclick="abrirModalNuevaNovedad()"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Novedad
        </button>
    </div>

    @php
        $columns = [
            'nombre_proyecto',
            'nombre_usuario',
            'createdAt',
            'fecha_respuesta',
            'estado',
            'acciones',
        ];

        $headers = [
            'nombre_proyecto' => 'Proyecto',
            'nombre_usuario' => 'Usuario asignado',
            'createdAt' => 'Fecha solicitud',
            'fecha_respuesta' => 'Fecha respuesta',
            'estado' => 'Estado',
            'acciones' => 'Acciones',
        ];

    @endphp

    <x-ui.table
        :data="$items"
        :columns="$columns"
        :headers="$headers"

        tableClass="border border-gray-200 rounded-lg"
        rowClass="h-[48px]"

        :columnClass="[
            'nombre_proyecto' => 'text-center',
            'nombre_usuario' => 'text-center',
            'createdAt' => 'text-center',
            'fecha_respuesta' => 'text-center',
            'estado' => 'text-center',
            'acciones' => 'text-center',
        ]"

        :customCells="[
            'nombre_proyecto' => fn($item) => e($item->proyecto->nombre_proyecto),

            'nombre_usuario' => fn($item) => e($item->user->nombre_completo),

            'createdAt' => fn($item) => e($item->createdAt),

            'fecha_respuesta' => fn($item) => e($item->fecha_respuesta),

            'estado' => fn($item) => $item->spanEstado,

            'acciones' => fn($item) =>
                view('novedades.partials.actions', compact('item'))->render(),
        ]"
    />

    @if($items->hasPages())
        {{ $items->links() }}
    @endif

    @include('novedades.modalComentario')
    @include('novedades.modalNovedades')

@endsection

@section('scripts')
    <script src="{{ asset('js/novedades/index.js') }}?v={{ filemtime(public_path('js/novedades/index.js')) }}"></script>
@endsection
