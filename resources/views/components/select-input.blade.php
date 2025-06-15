@props(['name', 'disabled' => false, 'options' => [], 'selected' => null, 'data' => null, 'datax' => false])

<select name="{{ $name }}" 
    {{ $disabled ? 'disabled' : '' }} 
    {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm']) }}>

    <option value="" {{ $attributes->has('required') ? 'disabled' : '' }} {{ (is_null($selected) || $selected == "") ? 'selected' : '' }}>
        -- Seleccione --
    </option>

    @if (is_array($options) && !empty($options))
        @if (!empty($data) && is_array($data))
            @foreach ($options as $option)
                <option 
                    value="{{ $option[$data[0]] }}" 
                    {{ $option[$data[0]] == old($name, $selected) ? 'selected' : '' }}
                    @if($datax)
                        data-datax='@json($option)'
                    @endif
                >
                    {{ strtolower($option[$data[1]]) }}
                </option>
            @endforeach
        @else
            @foreach ($options as $id => $nombre)
                <option value="{{ $id }}" {{ $id == old($name, $selected) ? 'selected' : '' }}>
                    {{ strtolower($nombre) }}
                </option>
            @endforeach
        @endif
    @else
        <option value="" disabled>No hay opciones disponibles</option>
    @endif
</select>
