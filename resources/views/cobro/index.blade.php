@extends('layouts.app')
@section('content')

    @php
        $columns = [
            'nombre_proyecto',
            'nombre_cliente',
            'telefono_cliente',
            'valor_pendiente',
            'fecha_acuerdo_pago',
            'dias_mora',
            'fecha_notificacion',
            'fecha_pago_cli',
            'estado',
            'acciones'
        ];

        $headers = [
            'nombre_proyecto' => 'Proyecto',
            'nombre_cliente' => 'Cliente',
            'telefono_cliente' => 'Teléfono Cliente',
            'valor_pendiente' => 'Valor Pendiente',
            'fecha_acuerdo_pago' => 'Fecha Acuerdo de Pago',
            'dias_mora' => 'Días de Mora',
            'fecha_notificacion' => 'Última Fecha Notificación',
            'fecha_pago_cli' => 'Fecha Pago Cliente',
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
            'nombre_cliente' => 'text-center',
            'telefono_cliente' => 'text-center',
            'valor_pendiente' => 'text-center',
            'fecha_acuerdo_pago' => 'text-center',
            'dias_mora' => 'text-center',
            'fecha_notificacion' => 'text-center',
            'fecha_pago_cli' => 'text-center',
            'estado' => 'text-center',
            'acciones' => 'text-center',
        ]"

        :customCells="[
            'nombre_proyecto' => fn($item) => e($item->proyecto->nombre_proyecto ?? '--'),
            'nombre_cliente' => fn($item) => e($item->proyecto->nombre_cliente ?? '--'),
            'telefono_cliente' => fn($item) => e($item->proyecto->telefono_cliente ?? '--'),
            'valor_pendiente' => fn($item) => e('$ ' . number_format($item->valor_pendiente ?? '--', 0)),
            'fecha_acuerdo_pago' => fn($item) => e($item->fecha_acuerdo_pago?? '--'),
            'dias_mora' => fn($item) => sprintf(
                '<span class=\'%s\'>%s</span>',
                ($item->dias_mora ?? 0) >= 0
                    ? 'text-green-600 font-semibold'
                    : 'text-red-600 font-semibold',
                e($item->dias_mora ?? '--')
            ),
            'fecha_notificacion' => fn($item) => e($item->fecha_notificacion?? '--'),
            'fecha_pago_cli' => fn($item) => e($item->fecha_pago_cli?? '--'),
            'estado' => fn($item) => $item->getSpanEstadoAttribute(),
            'acciones' => fn($item) =>
                view('cobro.partials.actions', compact('item'))->render(),
        ]"
    />

    @if($items->hasPages())
        {{ $items->links() }}
    @endif

    @include('cobro.modalNotificacion')
@endsection

@section('scripts')
    <script src="{{ asset('js/cobro/index.js') }}?v={{ filemtime(public_path('js/cobro/index.js')) }}"></script>
@endsection
