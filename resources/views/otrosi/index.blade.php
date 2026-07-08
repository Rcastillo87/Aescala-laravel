@extends('layouts.app')
@section('content')

    @include('otrosi.filter')
    <div class="flex justify-end text-center mb-3">
        @if (!Auth::user()->isUser)
            <x-secondary-button class="ms-4" href="{{ route('otro_si.create')}}">
                Crear Otrosi
            </x-secondary-button>
        @endif
    </div>
    <div class="relative overflow-x-auto rounded-lg border border-gray-200">
        <table class="w-full text-left text-sm text-gray-500">
            <x-table-header :headers="$headers" />
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ strtolower($item->proyecto->nombre_proyecto) }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ strtolower($item->user_encargado?->nombre_completo??'--') }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            N° {{ $item->numero }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item->fecha_creacion }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {!! $item->spanEstado !!}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            {{ $item->fecha_firma }}
                        </td>
                        <td class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            <div class=" flex items-center justify-center space-x-2">

                                <a tabindex="0"
                                    data-tooltip-target="tooltip-hover-contratoPdf-{{$item->id}}"
                                    data-tooltip-trigger="hover"
                                    href="{{ route('otro_si.otroSiPdf', $item->id) }}"
                                    target="_blank"
                                    class="flex items-center justify-center w-10 h-10 text-white bg-slate-700 hover:bg-white hover:text-slate-800 border-2 border-slate-800 focus:ring-4
                                        focus:outline-none focus:ring-slate-300 font-medium rounded-full text-sm dark:bg-slate-600 dark:hover:bg-slate-700 dark:focus:ring-slate-800">
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7h1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h11.5M7 14h6m-6 3h6m0-10h.5m-.5 3h.5M7 7h3v3H7V7Z"/>
                                    </svg>
                                </a>
                                <div id="tooltip-hover-contratoPdf-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                    PDF de Otrosí
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>

                                @if (!$item->img_firma && !Auth::user()->isUser)
                                    <a tabindex="0" data-tooltip-target="tooltip-hover-edit-{{$item->id}}" data-tooltip-trigger="hover"
                                    href="{{ route('otro_si.edit', ['id' => $item->id]) }}"
                                    class="beginProyec flex items-center justify-center w-10 h-10 text-white bg-green-700 hover:bg-white hover:text-green-800 border-2 border-green-800 focus:ring-4
                                            focus:outline-none focus:ring-green-300 font-medium rounded-full text-sm dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                                        </svg>
                                    </a>
                                    <div id="tooltip-hover-edit-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                        Editar
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                @endif

                                <!-- Botón link Firma -->
                                @if (!Auth::user()->isUser)
                                    <a tabindex="0" data-tooltip-target="tooltip-hover-encrip-{{$item->id}}" data-tooltip-trigger="hover" data-id="{{ $item->id }}"
                                        data-link="{{ $item->tokenEncrip }}" x-on:click="$dispatch('open-modal', 'sendLink-modal')" x-data="" onclick="setModalData(this)"
                                        class="flex items-center justify-center w-10 h-10 text-white bg-blue-700 hover:bg-white hover:text-blue-800 border-2 border-blue-800 focus:ring-4
                                            focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.213 9.787a3.391 3.391 0 0 0-4.795 0l-3.425 3.426a3.39 3.39 0 0 0 4.795 4.794l.321-.304m-.321-4.49a3.39 3.39 0 0 0 4.795 0l3.424-3.426a3.39 3.39 0 0 0-4.794-4.795l-1.028.961"/>
                                        </svg>
                                    </a>
                                    <div id="tooltip-hover-encrip-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                        Link Firma
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                @endif

                                @if ($item->estado == 2 && !Auth::user()->isUser)
                                    <!-- Botón link Firma -->
                                    <a tabindex="0"
                                        data-tooltip-target="tooltip-hover-recahzo-{{$item->id}}" data-tooltip-trigger="hover"
                                        x-on:click="
                                            $dispatch('open-modal', 'modalRechazoOtrosi-modal');
                                            $nextTick(() => {
                                                const textarea = document.getElementById('sugerencia_cliente');
                                                if (textarea) textarea.value = '{{ $item->sugerencia_cliente }}';
                                            });
                                        "
                                        x-data=""
                                        x-on:click="$dispatch('open-modal', 'modalRechazoOtrosi-modal')"

                                        class="flex items-center justify-center w-10 h-10 text-white bg-red-700 hover:bg-white hover:text-red-800 border-2 border-red-800 focus:ring-4
                                            focus:outline-none focus:ring-red-300 font-medium rounded-full text-sm dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                                        </svg>
                                    </a>
                                    <div id="tooltip-hover-recahzo-{{$item->id}}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium border-2 bg-white text-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                        Sugerencia de Rechazo
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                @endif

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) }}" class="py-2 truncate max-w-xs text-center bg-transparent border-b dark:border-white/40 shadow-transparent">
                            No hay registros.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Paginador -->
        @if($items->hasPages())
            <div class="mt-4">
                {{ $items->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
    @include('comercial.modalSendLink')
    @include('otrosi.modalRechazoOtrosi')
@endsection

@section('scripts')
    <script src="{{ asset('js/comercial/index.js') }}?v={{ filemtime(public_path('js/comercial/index.js')) }}"></script>
@endsection
