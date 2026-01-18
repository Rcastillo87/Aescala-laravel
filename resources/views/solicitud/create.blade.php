@extends('layouts.app')
@section('content')
    <div class="relative w-full h-full px-2 overflow-y-hidden">
        <form method="POST" id="formSolicitud" action="{{ route('solicitud.save') }}">
            @csrf
            <div class="flex flex-col lg:flex-row gap-4">
                <!-- Primera columna -->
                <div class="w-full lg:w-1/2 space-y-4 p-3 border-2 border-gray-400 rounded-2xl">

                    <input class="hidden" value="{{ Auth::user()->id }}" id="id_user" name="id_user">

                    @if (!Auth::user()->isAnalista)
                        <div>
                            <x-input-label for="id_fases" :value="__('Seleccione la fase')" />
                            <x-select-input 
                                id="id_fases"
                                name="id_fases" 
                                :options="$fases"
                                :datax="true"
                                :data="['id', 'name_fase']"
                                class="block mt-1 w-full" 
                            />
                        </div>
                    @endif

                    <div>
                        <x-input-label for="id_proyecto" :value="__('Seleccione Proyecto *')" />
                        <x-select-input 
                            placeholder="Busqueda.."
                            autocomplete="off"
                            name="id_proyecto" 
                            id="id_proyecto"
                            :options="$proyectos" 
                            :data="['id', 'nombre_proyecto']"
                            :selected="old('id_proyecto')" 
                            class="block mt-1 w-full"
                        />
                        <x-input-error :messages="$errors->get('id_proyecto')" class="mt-2" />
                    </div>
                    <div>
                        <input class="hidden" value="{{ json_encode($materiales) }}"  id="arrayMateriales" name="arrayMateriales" disabled>
                        <x-input-label for="id_material" :value="__('Seleccione Material *')" />
                        <x-select-input 
                            placeholder="Busqueda.."
                            autocomplete="off"
                            name="id_material" 
                            id="id_material"
                            :options="$materiales" 
                            :data="['id', 'nombre_material']"
                            :selected="old('id_material')" 
                            class="block mt-1 w-full"
                        />
                        <x-input-error :messages="$errors->get('id_material')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="observacion" :value="__('Observaciones')" />
                        <textarea
                            id="observacion"
                            name="observacion"
                            autofocus
                            oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px';"
                            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 
                            focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full overflow-hidden resize-none leading-6 py-2"
                            >{{old('observacion')}}</textarea>
                        <x-input-error :messages="$errors->get('observacion')" class="mt-2" />
                    </div>
                </div>

                <!-- Segunda columna -->
                <div class="w-full lg:w-1/2 text-center border-2 border-gray-400 rounded-2xl flex flex-col">
                    <p class="font-bold text-xl mb-3 shrink-0">
                        Materiales a solicitar
                    </p>
                    <!-- CONTENEDOR CON SCROLL -->
                    <div
                        id="selectMateriales"
                        class="flex-1 overflow-y-auto px-2"
                        style="max-height: 70vh"
                    ></div>
                </div>
            </div>

            <!-- Botón alineado a la derecha siempre en la parte inferior -->
            <div class="flex justify-end mt-6 space-x-4">
                @isroute('solicitud.create')
                    <a  href="#" id="btnLimpiar" class="px-4 py-2 bg-red-500 hover:bg-red-400 rounded-lg flex items-center text-white">
                        <svg class="w-6 h-6 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"/>
                        </svg>
                        Limpiar
                    </a>
                @endisroute
                <a href="{{ route('solicitud.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg">Cancelar</a>
                <x-primary-button id="btnSubmit" type="submit">
                    Guardar
                </x-primary-button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/solicitud/create.js') }}"></script>
@endsection
