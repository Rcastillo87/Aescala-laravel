@extends('layouts.app')

@section('content')
<div class="w-full h-full p-4">

    {{-- Info solicitud --}}
    <div class="bg-white shadow rounded-lg p-4 mb-4 border">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2 text-sm">
            <div>
                <p class="font-semibold text-gray-600">Proyecto</p>
                <p class="text-gray-800">{{ $solicitud->proyecto->nombre_proyecto}}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-600">Nombre Cliente</p>
                <p class="text-gray-800">{{ $solicitud->proyecto->nombre_cliente }}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-600">Fecha Solicitud</p>
                <p class="text-gray-800">{{ $solicitud->fecha_solicitud }}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-600">Estado</p>
                <p class="text-gray-800">{!! $solicitud->span_estado !!}</p>
            </div>
        </div>
    </div>

    <form method="POST" id="formSolicitud" action="{{ route('solicitud.saveSolicitud') }}">
        @csrf
        <input type="hidden" name="id_solicitud" value="{{ $solicitud->id }}">

        {{-- Contenedor solicitado por ti --}}
        <div class="w-full h-full text-center border-2 border-gray-400 rounded-2xl p-4">
            <p class="font-bold text-xl mb-3">Materiales Solicitados</p>

            {{-- Lista de items: UNA COLUMNA SIEMPRE --}}
            <div id="itemsContainer" class="w-full flex items-stretch gap-2">
                @foreach($solItemsArray as $key => $m)

                    <div class="item-card lg:w-1/2 bg-white px-2 py-1 border-2 m-1 space-y-1 border-blue-500 rounded-xl">
                        <div class="flex items-center">
                            <p class="text-md font-bold text-gray-500">Material:
                                <span class="ml-2 text-black">{{$m['nombre_material']}}</span>
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 w-full">

                            <!-- Inventario -->
                            <div class="flex items-center text-[12px] font-bold px-2 h-8
                                        text-white rounded bg-gradient-to-tr from-red-600 to-red-400
                                        whitespace-nowrap inventario-value">
                                En inventario: {{ $m['unidades'] }} {{ $m['cantidad_inventario'] }}
                            </div>

                            <!-- Pendiente -->
                            <div class="flex items-center text-[12px] font-bold px-2 h-8 border border-gray-300 
                                        rounded bg-white whitespace-nowrap">
                                Pendiente: <span class="pendiente-value">{{ $m['pendiente'] }}</span>
                            </div>

                            <!-- Solicitado -->
                            <label class="flex items-center gap-1 text-[12px] font-bold border border-gray-300 
                                        rounded bg-white px-2 h-8 flex-grow min-w-[150px]">
                                <span class="whitespace-nowrap">Solicitado:</span>
                                <input 
                                    type="number"
                                    value="{{ $m['cantidad_solicitada'] }}"
                                    max="{{ min( $m['cantidad_inventario'], $m['cantidad_solicitada'] ) }}"
                                    min="{{ min( $m['cantidad_inventario'], 1)}}"
                                    name="materiales[{{ $key }}][cantidad]"
                                    
                                    data-inv="{{ $m['cantidad_solicitada'] }}"

                                    class="text-[12px] border border-gray-300 rounded-md shadow-sm w-full h-7 px-1 input-cantidad"
                                >
                            </label>
                        </div>

                        <div class="flex justify-between">
                            <div class="flex items-center">
                                <input type="hidden" name="materiales[{{$key}}][cancelo]" value="0">
                                <input
                                    id="materiales_{{$key}}_cancelo"
                                    type="checkbox"
                                    name="materiales[{{$key}}][cancelo]"
                                    value="1"
                                    class="w-4 h-4 text-blue-600"
                                >
                                <label for="materiales_{{$key}}_cancelo" class="ml-2 text-sm font-medium">
                                    Cancelar Despacho
                                </label>
                            </div>
                            <p>Tipo: {!! $m['spanTipo'] !!} </p>
                        </div>

                        <div class="flex bg-gradient-to-r justify-between from-slate-200 to-slate-100 rounded p-1 w-full">
                            <p class="text-md font-bold text-left text-gray-500">Observación:
                                <small class="ml-2 text-black">{{$m['descripccion']}}</small>
                            </p>
                        </div>
                        <input type="hidden" name="materiales[{{$key}}][id_material]" value="{{ $m['id_material'] }}">
                    </div>

                @endforeach
            </div> 

            <div class="mt-4 flex justify-end gap-3">
                <a href="{{ route('solicitud.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg">Cancelar</a>
                <button type="submit" id="btnEnviar" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Despachar</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
    <script src="{{asset('js/solicitud/createDespachoSolicitud.js')}}"></script>
@endsection
