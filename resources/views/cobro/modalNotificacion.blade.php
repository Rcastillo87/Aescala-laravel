<x-modal name="notificacion-modal" maxWidth="lg">
    <div x-data="whatsappData()" class="p-6">
        <h2 class="text-lg font-bold mb-4">Enviar Notificación</h2>
        
        <label class="block text-sm font-medium">Teléfono:</label>
        <input type="text" x-model="telefono" class="w-full border rounded mb-3">
        
        <label class="block text-sm font-medium">Mensaje:</label>
        <textarea x-model="mensaje" rows="8" class="w-full border rounded mb-4"></textarea>

        <div class="flex justify-end gap-2">
            <button x-on:click="$dispatch('close-modal', 'notificacion-modal')" class="px-4 py-2 bg-gray-300 rounded">Cancelar</button>
            <button x-on:click="enviar()" class="px-4 py-2 bg-green-600 text-white rounded">Enviar por WhatsApp</button>
        </div>
    </div>
</x-modal>