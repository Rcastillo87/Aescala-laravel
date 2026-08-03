@extends('layouts.app')
@section('content')
    <div class="mx-2">
        <div class="flex-1 min-w-0">
            <div class="py-1 mb-1 border-b border-gray-100 flex items-center justify-between">

                <div class="">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Proyecto</span>
                    <h3 class="text-base sm:text-lg font-bold text-gray-800 leading-tight truncate">
                        {{ $proyecto->nombre_proyecto }}
                    </h3>
                </div>

                <div class="">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Estado</span>
                    <div class="flex flex-wrap items-center">
                        {!! $proyecto->span_estado !!}
                    </div>
                </div>

            </div>
 
            {{-- Grid de datos --}}
            <div class="grid grid-cols-1 xs:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-x-4 gap-y-3">

                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Ubicación</p>
                    <p class="text-sm text-gray-700 leading-snug">
                        {{ $departamentos[intval($proyecto['departamento'])]['departamento'] }} –
                        {{ $departamentos[intval($proyecto['departamento'])]['ciudades'][$proyecto['ciudad']] }} –
                        {{ $proyecto->ubicacion !== null ? ($ubicacion[$proyecto->ubicacion] ?? 'N/A') : 'N/A' }}
                    </p>
                </div>

                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Dirección</p>
                    <p class="text-sm text-gray-700 leading-snug truncate">{{ $proyecto->direccion }}</p>
                </div>

                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Contacto Cliente</p>
                    <p class="text-sm text-gray-700 leading-snug">{{ $proyecto->nombre_cliente }}</p>
                </div>

                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Teléfono</p>
                    <p class="text-sm text-gray-700 leading-snug font-mono">{{ $proyecto->telefono_cliente }}</p>
                </div>

                @if($proyecto->user)
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Residente</p>
                        <p class="text-sm text-gray-700 leading-snug">{{ $proyecto->user?->nombre_completo }}</p>
                    </div>
                @endif

                @if($proyecto->userOB)
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Cont. Obra Blanca</p>
                        <p class="text-sm text-gray-700 leading-snug">{{ $proyecto->userOB['nombre_completo'] }}</p>
                    </div>
                @endif

                @if($proyecto->user_carpinteria)
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Cont. Carpintería</p>
                        <p class="text-sm text-gray-700 leading-snug">{{ $proyecto->user_carpinteria }}</p>
                    </div>
                @endif

                @if($proyecto->userDiseno)
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Diseñador</p>
                        <p class="text-sm text-gray-700 leading-snug">{{ $proyecto->userDiseno['nombre_completo'] }}</p>
                    </div>
                @endif

                @if($proyecto->fec_fin_real)
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Fecha de Entrega</p>
                        <p class="text-sm text-gray-700 leading-snug font-medium">{{ explode(' ', $proyecto->fec_fin_real)[0] }}</p>
                    </div>
                @endif

                @if($proyecto->paz_salvo == 1)
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Paz & salvo</p>
                        <p class="text-sm text-gray-700 leading-snug font-medium">{!! $proyecto->apaz !!}</p>
                    </div>
                @endif

                @if($proyecto->observacion)
                    <div class="min-w-0 xs:col-span-2 md:col-span-3 xl:col-span-4">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-0.5">Observación</p>
                        <p class="text-sm text-gray-600 leading-snug italic">{{ $proyecto->observacion }}</p>
                    </div>
                @endif

            </div>
        </div>

        <hr class="my-4 border-gray-200">

        


        

    </div>
@endsection

@section('scripts')
@endsection