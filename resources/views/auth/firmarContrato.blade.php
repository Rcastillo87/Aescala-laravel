<x-guest-layout>
    <div class="flex items-center justify-center min-h-screen w-full bg-gray-100 px-4">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-5xl w-full text-center relative">
            <!-- Logo -->
            <div class="flex justify-center mb-3">
                <img src="{{ asset('img/logo.png') }}" alt="logo" class="w-[200px] h-[100px] object-contain">
            </div>

            <!-- Título -->
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Contrato Aescala</h1>

            <!-- Subtítulo -->
            <p class="text-center text-gray-600 text-sm">
                Términos, Condiciones y Obligaciones del Contrato
            </p>

            {{-- Contenido del contrato --}}
            <div class="contrato-container border border-gray-300 rounded-xl p-4 mt-6">
                {{-- Aquí va el contenido del contrato --}}
                @include('proyecto.pdfContrato', $data)
            </div>

        </div>
    </div>
</x-guest-layout>
