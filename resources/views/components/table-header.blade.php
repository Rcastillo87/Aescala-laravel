@props(['headerClass' => ''])
<thead class="align-bottom {{ $headerClass }}">
    <tr>
        @foreach($headers as $header)
            <th class="px-6 py-3 text-center font-bold uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-600 opacity-70">
                {{ $header }}
            </th>
        @endforeach
    </tr>
</thead>
