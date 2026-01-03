<x-modal name="tratamiento-datos-modal" maxWidth="2xl">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">
            Tratamiento de Datos Personales
        </h2>

        <div class="text-sm text-gray-600 max-h-72 overflow-y-auto space-y-3">
            <p>
                Autorizo de manera libre, previa, expresa y voluntaria el tratamiento
                de mis datos personales conforme a la Ley 1581 de 2012.
            </p>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <button
                type="button"
                class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500"
                x-on:click="
                    $dispatch('revocar-tratamiento');
                    $dispatch('close-modal', 'tratamiento-datos-modal');
                "
            >
                Revocar
            </button>

            <button
                type="button"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700"
                x-on:click="
                    $dispatch('aceptar-tratamiento');
                    $dispatch('close-modal', 'tratamiento-datos-modal');
                "
            >
                Aceptar
            </button>
        </div>
    </div>
</x-modal>
