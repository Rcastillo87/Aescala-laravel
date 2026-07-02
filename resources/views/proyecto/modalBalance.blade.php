<!-- Modal -->
<x-modal name="balance-modal" maxWidth="6xl" :closeOnOutsideClick="false" :closeOnEscape="false">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-4">Balance General</h2>
        <div id="project-balance" class="space-y-4 mb-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Carpintería -->
                <div class="w-full p-4 border rounded-lg shadow-sm">
                    <h3 class="text-lg font-bold text-gray-700 flex items-center">
                        <!-- Icono -->
                        BALANCE CARPINTERÍA
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <div class="p-2">
                            <h4 class="font-semibold">DINERO</h4>
                            <div class="ml-6 space-y-1">
                                <p id="carpinteria-presupuesto" class="text-green-600">PRESUPUESTO: $0</p>
                                <p id="carpinteria-gastos-dinero" class="text-red-600">GASTOS: $0</p>
                                <hr class="border-gray-200 my-1 w-32">
                                <p id="carpinteria-disponible-dinero" class="text-gray-800 font-medium">DISPONIBLE: $0</p>
                            </div>
                        </div>
                        <div class="p-2">
                            <h4 class="font-semibold">MATERIALES</h4>
                            <div class="ml-6 space-y-1">
                                <p id="carpinteria-presupuesto-material" class="text-green-600">PRESUPUESTO: $0</p>
                                <p id="carpinteria-gastos-material" class="text-red-600">GASTOS: $0</p>
                                <hr class="border-gray-200 my-1 w-32">
                                <p id="carpinteria-disponible-material" class="text-gray-800 font-medium">DISPONIBLE: $0</p>
                            </div>
                        </div>
                    </div>
                </div>
        
                <!-- Obra Blanca -->
                <div class="w-full p-4 border rounded-lg shadow-sm">
                    <h3 class="text-lg font-bold text-gray-700 flex items-center">
                        BALANCE OBRA BLANCA
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <div class="p-2">
                            <h4 class="font-semibold">DINERO</h4>
                            <div class="ml-6 space-y-1">
                                <p id="obrablanca-presupuesto" class="text-green-600">PRESUPUESTO: $0</p>
                                <p id="obrablanca-gastos-dinero" class="text-red-600">GASTOS: $0</p>
                                <hr class="border-gray-200 my-1 w-32">
                                <p id="obrablanca-disponible-dinero" class="text-gray-800 font-medium">DISPONIBLE: $0</p>
                            </div>
                        </div>
                        <div class="p-2">
                            <h4 class="font-semibold">MATERIALES</h4>
                            <div class="ml-6 space-y-1">
                                <p id="obrablanca-presupuesto-material" class="text-green-600">PRESUPUESTO: $0</p>
                                <p id="obrablanca-gastos-material" class="text-red-600">GASTOS: $0</p>
                                <hr class="border-gray-200 my-1 w-32">
                                <p id="obrablanca-disponible-material" class="text-gray-800 font-medium">DISPONIBLE: $0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Otros -->
                <div class="w-full p-4 border rounded-lg shadow-sm">
                    <h3 class="text-lg font-bold text-gray-700 flex items-center">
                        BALANCE OTROS
                    </h3>
                    <div class="mt-3 ml-6 space-y-1">
                        <p id="otros-presupuesto" class="text-green-600">PRESUPUESTO: $0</p>
                        <p id="otros-gastos" class="text-red-600">GASTOS PAGOS: $0</p>
                        <p id="otros-gastos-material" class="text-red-600">GASTOS MATERIAL: $0</p>
                        <hr class="border-gray-200 my-1 w-48">
                        <p id="otros-disponible" class="text-gray-800 font-medium">DISPONIBLE: $0</p>
                    </div>
                </div>
        
                <!-- General -->
                <div class="w-full p-4 border rounded-lg shadow-sm">
                    <h3 class="text-lg font-bold text-gray-700 flex items-center">
                        BALANCE GENERAL DEL PROYECTO
                    </h3>
                    <div class="mt-3 ml-6 space-y-1">
                        <p id="global-presupuesto" class="text-blue-600">PRESUPUESTO TOTAL: $0</p>
                        <p id="global-abonos" class="text-green-600">ABONOS TOTALES: $0</p>
                        <p id="global-gastos" class="text-red-600">GASTOS TOTALES: $0</p>
                        <hr class="border-gray-200 my-1 w-48">
                        <p id="global-rentabilidad" class="text-gray-800 font-medium">RENTABILIDAD: $0</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-start">
            <a tabindex="0" href="#"
                x-on:click="$dispatch('close-modal', 'balance-modal')"
                class="bg-red-500 text-white px-4 py-2 rounded"
            >
                Cerrar
            </a>
        </div>
    </div>
</x-modal>