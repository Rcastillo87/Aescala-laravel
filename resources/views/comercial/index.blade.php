@extends('layouts.app')
@section('content')
    <div class="flex justify-end text-center mb-3">
        <x-secondary-button class="ms-4" href="{{ route('comercial.create')}}">
            Crear Proyecto
        </x-secondary-button>
    </div>
    @include('comercial.filter')

    @php
        $columns = [
            'id',
            'nombre_proyecto',
            'nombre_cliente',
            'ubicacion',
            'direccion',
            'telefono_cliente',
            'estado',
            'acciones',
        ];
        $headers = [
            'id'               => 'ID',
            'nombre_proyecto'  => 'Nombre Proyecto',
            'nombre_cliente'   => 'Nombre Cliente',
            'ubicacion'        => 'Ubicación',
            'direccion'        => 'Dirección',
            'telefono_cliente' => 'Teléfono',
            'estado'           => 'Estado',
            'acciones'         => 'Opciones',
        ];
        $customCells = [
            'ubicacion' => function ($item) use ($departamentos) {
                $depIndex = (int) $item->departamento;
                $ciudadIndex = (int) $item->ciudad;

                $dep = isset($departamentos[$depIndex])
                    ? e($departamentos[$depIndex]['departamento'])
                    : 'N/A';

                $ciudad = isset($departamentos[$depIndex]['ciudades'][$ciudadIndex])
                    ? e($departamentos[$depIndex]['ciudades'][$ciudadIndex])
                    : 'N/A';

                return '<span class="text-md">' . $dep . ' - ' . $ciudad . '</span>';
            },
            'estado' => function ($item) {
                return $item->spanEStado0;
            },
            'acciones' => function ($item) {
                return view('comercial.partials.actions', compact('item'));
            },
        ];
    @endphp

    <x-ui.table
        :data="$items"
        :columns="$columns"
        :headers="$headers"
        tableClass="border border-gray-200 rounded-lg"
        rowClass="h-[50px]"
        :columnClass="[
            'id'               => 'text-center',
            'nombre_proyecto'  => 'text-center',
            'nombre_cliente'   => 'text-center',
            'ubicacion'        => 'text-center',
            'direccion'        => 'text-center',
            'telefono_cliente' => 'text-center',
            'estado'           => 'text-center',
            'acciones'         => 'text-center',
        ]"
        :customCells="$customCells"
    />

    <!-- Paginador -->
    @if($items->hasPages())
        {{ $items->links() }}
    @endif

    @include('comercial.modalSendLink')
@endsection

@section('scripts')
    <script src="{{ asset('js/comercial/index.js') }}?v={{ filemtime(public_path('js/comercial/index.js')) }}"></script>
@endsection

