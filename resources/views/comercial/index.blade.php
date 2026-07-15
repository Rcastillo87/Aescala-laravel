@extends('layouts.app')
@section('content')

    <div class="flex justify-end text-center mb-3">

        @if (Auth::user()->isAdmin || Auth::user()->isComer)

            <x-primary-button class="" href="#"
                data-tooltip-target="tooltip-hover-Entregable" data-tooltip-trigger="hover"
                x-data="" x-on:click="$dispatch('open-modal', 'modalConfigEntre')">
                <svg class="w-6 h-6 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13v-2a1 1 0 0 0-1-1h-.757l-.707-1.707.535-.536a1 1 0 0 0 0-1.414l-1.414-1.414a1 1 0 0 0-1.414 0l-.536.535L14 4.757V4a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v.757l-1.707.707-.536-.535a1 1 0 0 0-1.414 0L4.929 6.343a1 1 0 0 0 0 1.414l.536.536L4.757 10H4a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h.757l.707 1.707-.535.536a1 1 0 0 0 0 1.414l1.414 1.414a1 1 0 0 0 1.414 0l.536-.535 1.707.707V20a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-.757l1.707-.708.536.536a1 1 0 0 0 1.414 0l1.414-1.414a1 1 0 0 0 0-1.414l-.535-.536.707-1.707H20a1 1 0 0 0 1-1Z"/>
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                </svg>
                Administrar Entregables
            </x-primary-button>
            <div id="tooltip-hover-Entregable" role="tooltip"
                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                Añadir Entregable
                <div class="tooltip-arrow" data-popper-arrow></div>
            </div>
        @endif


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
    @include('comercial.modalConfigEntre')
@endsection

@section('scripts')
    <script src="{{ asset('js/comercial/index.js') }}?v={{ filemtime(public_path('js/comercial/index.js')) }}"></script>
@endsection

