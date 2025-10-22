@props([
    'id',
    'name',
    'label' => null,
    'accept' => '',
    'required' => false,
    'error' => null,
])

<div class="w-full">
    @if ($label)
        <label for="{{ $id }}" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
            {{ $label }}
            @if($required)<span class="text-red-600">*</span>@endif
        </label>
    @endif

    <div class="relative mt-1">
        <!-- Input oculto -->
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="file"
            accept="{{ $accept }}"
            class="hidden"
            {{ $required ? 'required' : '' }}
            {{ $attributes }}
            onchange="mostrarNombreArchivo('{{ $id }}')"
        >

        <!-- Contenedor visual -->
        <div
            class="flex items-center justify-between border border-gray-300 dark:border-gray-700 dark:bg-gray-900
                   rounded-md shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 dark:focus-within:ring-indigo-600
                   focus-within:border-indigo-500 dark:focus-within:border-indigo-600 h-[42px]"
        >
            <span id="{{ $id }}_nombre"
                  class="block w-full px-3 text-sm text-gray-700 dark:text-gray-300 truncate leading-[42px]">
                No se ha seleccionado ningún archivo
            </span>

            <label
                for="{{ $id }}"
                class="h-full flex items-center px-4 text-sm font-medium text-white bg-indigo-600 rounded-r-md cursor-pointer hover:bg-indigo-700 focus:outline-none"
            >
                Seleccionar
            </label>
        </div>
    </div>

    @if ($error)
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $error }}</p>
    @endif
</div>

@once
<script>
    function mostrarNombreArchivo(id) {
        const input = document.getElementById(id);
        const span = document.getElementById(`${id}_nombre`);
        const file = input.files[0];
        span.textContent = file ? file.name : 'No se ha seleccionado ningún archivo';
    }
</script>
@endonce