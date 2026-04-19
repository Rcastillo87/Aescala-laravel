@extends('layouts.app')
@section('content')

    <div class="flex justify-end text-center mb-3">
        <x-secondary-button class="ms-4" href="{{ route('almacen.create')}}">
            Crear Usuario
        </x-secondary-button>
    </div>

    @php

        $columns = [
            'id',
            'nombre_almacen',
            'tipo',
            'editar',
            'id_user',
            'created_at',
            'acciones',
        ];

        /** headers con diseño */
        $headers = [
            'id'            => 'ID',
            'nombre_almacen' => 'Nombre Almacen',
            'tipo'          => 'Tipo',
            'editar'        =>'Permite Editar Cantidades',
            'id_user'       => 'Encargado Almacen',
            'created_at'    => 'Fecha de Creacion',
            'acciones'      => 'Opciones',
        ];
    @endphp

    <x-ui.table
        :data="$items"
        :columns="$columns"
        :headers="$headers"

        tableClass="border border-gray-200 rounded-lg"
        rowClass="h-[50px]"

        :customCells="[
            'id_user'=> fn($item) => $item->user->nombre_completo ?? 'N/A',
            'tipo' => fn($item) => $item->spanTipo,
            'editar' => fn($item) => $item->spanEditar,
            'acciones' => fn($item) => view('almacen.partials.actions', compact('item'))->render(),
        ]"
    />

@endsection
