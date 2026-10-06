<x-filament-panels::page>
    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
        @foreach (['total' => 'Total tenders', 'open' => 'Open', 'closed' => 'Closed', 'awarded' => 'Awarded', 'cancelled' => 'Cancelled', 'overdue_close' => 'Past closing, not closed', 'published' => 'Published', 'drafts' => 'Drafts'] as $key => $label)
            <div class="rounded-xl bg-white p-4 shadow dark:bg-gray-900">
                <p class="text-sm text-gray-500">{{ $label }}</p>
                <p class="text-2xl font-bold">{{ $summary[$key] ?? 0 }}</p>
            </div>
        @endforeach
    </div>
    <p class="mt-4 text-sm text-gray-500">Closing-date compliance: tenders past their closing date that are not explicitly closed, awarded, or cancelled require administrator action.</p>
</x-filament-panels::page>
