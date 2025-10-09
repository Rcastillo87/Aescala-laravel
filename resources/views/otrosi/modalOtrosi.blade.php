<!-- Modal -->
<x-modal name="modalOtrosi-modal" maxWidth="4xl">
    <div class="p-6">

        <h2 class="mb-3 text-xl font-semibold" id="txLabelOtroSi">Crear Otro Si</h2>

        <form method="POST" id="formOtroSi" action="{{ route('otro_si.save') }}">
            @csrf
            <div class="flex flex-wrap border border-gray-200 rounded-lg">
                <input type="hidden" name="id_otrosi" id="id_otrosi">
                <input type="hidden" name="id_user_encar" id="id_user_encar" value="{{ Auth::user()->id }}">
                
                <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-6/12 2xl:w-4/12 md:flex-0">
                    <x-input-label for="id_proyecto" :value="__('Proyecto *')" />
                    <x-select-input 
                        name="id_proyecto" 
                        id="id_proyecto"
                        :options="$proyectos" 
                        :data="['id', 'nombre_proyecto']"
                        :selected="old('id_proyecto')" 
                        class="block mt-1 w-full" 
                        required
                    />
                    <x-input-error :messages="$errors->get('id_proyecto')" class="mt-2" />
                </div>


                <div class="flex w-full justify-between m-2">
                    <a  href="#" tabindex="0"
                        x-on:click="$dispatch('close-modal', 'modalOtrosi-modal')"
                        class="bg-red-500 text-white px-4 py-2 rounded"
                    >
                        Cerrar
                    </a>
                    <x-primary-button class="ms-4">
                        Guardar
                    </x-primary-button>
                </div>
            </div>
        </form>

    </div>
</x-modal>