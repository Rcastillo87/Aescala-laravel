@props([
    'data' => [],
    'columns' => [],
    'headers' => [],
    'customCells' => [],
    'tableClass' => '',
    'rowClass' => '',
    'columnClass' => [],
    'headerClass' => [],
])

<div class="relative overflow-x-auto rounded-lg border border-gray-200">
    <table class="min-w-max text-sm text-gray-700 {{ $tableClass }}">
        <thead class="bg-orange-500 text-xs text-white uppercase">
            <tr>
                @foreach($columns as $col)
                    <th class="px-4 py-3 text-center whitespace-nowrap {{ $headerClass[$col] ?? '' }}">
                        {{ $headers[$col] ?? ucfirst($col) }}
                    </th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @forelse($data as $row)
                <tr class="border-b hover:bg-gray-50 {{ $rowClass }} {!! $row->RowBgClass ?? '' !!}">
                    @foreach($columns as $col)
                        <td class="px-4 py-2 text-center align-middle whitespace-nowrap {{ $columnClass[$col] ?? '' }}">
                            @if(isset($customCells[$col]))
                                {!! $customCells[$col]($row) !!}
                            @else
                                {!! data_get($row, $col, '') !!}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) }}" class="py-6 text-center text-gray-400">
                        No hay datos disponibles
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>