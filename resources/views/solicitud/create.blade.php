@extends('layouts.app')
@section('content')

    <div class="relative w-full h-full px-2 overflow-y-hidden">
        <form method="POST" id="formSolicitud" action="{{ route('solicitud.save') }}">
            @csrf
            <div class="flex flex-col lg:flex-row gap-4">
                <!-- Primera columna -->
                <div class="w-full lg:w-1/2 space-y-4 p-3 border-2 border-gray-400 rounded-2xl">

                    <input class="hidden" value="{{ Auth::user()->id }}" id="id_user" name="id_user">
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
                <div class="w-full h-full lg:w-1/2 text-center border-2 border-gray-400 rounded-2xl">
                    <p class="font-bold text-xl mb-3">Materiales a solicitar</p>
                    <div id="selectMateriales"></div>
                </div>
            </div>

            <!-- Botón alineado a la derecha siempre en la parte inferior -->
            <div class="flex justify-end mt-6">
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