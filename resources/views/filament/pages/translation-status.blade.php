<x-filament-panels::page>
    <p class="mb-4 text-sm text-gray-500">English is the authoritative source. Shona and Ndebele rows fall back to English wherever a translation is missing. Only council-approved wording should be entered.</p>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left">
                    <th class="p-2">Content</th>
                    <th class="p-2">Records</th>
                    <th class="p-2">Shona complete</th>
                    <th class="p-2">Shona partial</th>
                    <th class="p-2">Ndebele complete</th>
                    <th class="p-2">Ndebele partial</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($report as $row)
                    <tr class="border-t">
                        <td class="p-2 font-semibold">{{ $row['label'] }}</td>
                        <td class="p-2">{{ $row['total'] }}</td>
                        <td class="p-2">{{ $row['sn_complete'] }}</td>
                        <td class="p-2">{{ $row['sn_partial'] }}</td>
                        <td class="p-2">{{ $row['nd_complete'] }}</td>
                        <td class="p-2">{{ $row['nd_partial'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
