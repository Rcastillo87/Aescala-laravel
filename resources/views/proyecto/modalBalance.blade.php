<!-- Modal -->
<x-modal name="balance-modal" maxWidth="6xl">
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-4">Balance General</h2>

        <div id="project-balance" class="space-y-4 mb-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Carpintería -->
                <div class="w-full p-4 border rounded-lg shadow-sm">
                    <h3 class="text-lg font-bold text-gray-700 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                        BALANCE CARPINTERÍA
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <div class="p-2">
                            <h4 class="font-semibold flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                DINERO
                            </h4>
                            <div class="ml-6 space-y-1">
                                <p class="text-green-600">PRESUPUESTO: $0</p>
                                <p class="text-red-600">GASTOS: $0</p>
                                <hr class="border-gray-200 my-1 w-32">
                                <p class="text-gray-800 font-medium">DISPONIBLE: $0</p>
                            </div>
                        </div>
                        <div class="p-2">
                            <h4 class="font-semibold flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                MATERIALES
                            </h4>
                            <div class="ml-6 space-y-1">
                                <p class="text-green-600">PRESUPUESTO: $0</p>
                                <p class="text-red-600">GASTOS: $0</p>
                                <hr class="border-gray-200 my-1 w-32">
                                <p class="text-gray-800 font-medium">DISPONIBLE: $0</p>
                            </div>
                        </div>
                    </div>
                </div>
        
                <!-- Obra Blanca -->
                <div class="w-full p-4 border rounded-lg shadow-sm">
                    <h3 class="text-lg font-bold text-gray-700 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                        BALANCE OBRA BLANCA
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <div class="p-2">
                            <h4 class="font-semibold flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                DINERO
                            </h4>
                            <div class="ml-6 space-y-1">
                                <p class="text-green-600">PRESUPUESTO: $0</p>
                                <p class="text-red-600">GASTOS: $0</p>
                                <hr class="border-gray-200 my-1 w-32">
                                <p class="text-gray-800 font-medium">DISPONIBLE: $0</p>
                            </div>
                        </div>
                        <div class="p-2">
                            <h4 class="font-semibold flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                MATERIALES
                            </h4>
                            <div class="ml-6 space-y-1">
                                <p class="text-green-600">PRESUPUESTO: $0</p>
                                <p class="text-red-600">GASTOS: $0</p>
                                <hr class="border-gray-200 my-1 w-32">
                                <p class="text-gray-800 font-medium">DISPONIBLE: $0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Otros -->
                <div class="w-full p-4 border rounded-lg shadow-sm">
                    <h3 class="text-lg font-bold text-gray-700 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                        BALANCE OTROS
                    </h3>
                    <div class="mt-3 ml-6 space-y-1">
                        <p class="text-green-600">PRESUPUESTO: $1,000,000</p>
                        <p class="text-red-600">GASTOS: $0</p>
                        <hr class="border-gray-200 my-1 w-48">
                        <p class="text-gray-800 font-medium">DISPONIBLE: $1,000,000</p>
                    </div>
                </div>
        
                <!-- General -->
                <div class="w-full p-4 border rounded-lg shadow-sm">
                    <h3 class="text-lg font-bold text-gray-700 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                        BALANCE GENERAL DEL PROYECTO
                    </h3>
                    <div class="mt-3 ml-6 space-y-1">
                        <p class="text-blue-600">PRESUPUESTO TOTAL: $1,000,000</p>
                        <p class="text-green-600">ABONOS TOTALES: $0</p>
                        <p class="text-red-600">GASTOS TOTALES: $0</p>
                        <hr class="border-gray-200 my-1 w-48">
                        <p class="text-gray-800 font-medium">RENTABILIDAD: $0</p>
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