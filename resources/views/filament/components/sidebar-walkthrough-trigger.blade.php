@php
    $totalTasks = collect(\App\Filament\Pages\SystemGuide::getWorkflowCatalog())->sum(fn($w) => count($w['tasks']));
@endphp

<div class="mrdc-sidebar-walkthrough-container px-3 py-3 border-t border-slate-200/80 dark:border-slate-800">
    <button
        type="button"
        @click="$dispatch('open-walkthrough-drawer')"
        class="w-full group flex items-center justify-between gap-2.5 px-3 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-400 text-white transition-all text-left shadow-md cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1"
    >
        <div class="flex items-center gap-2.5 min-w-0">
            <span class="flex-shrink-0 w-7 h-7 rounded-md bg-white/20 text-white flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </span>
            <div class="truncate">
                <div class="text-xs font-bold leading-tight text-white">
                    System Walkthrough
                </div>
                <div class="text-[10px] text-emerald-50 dark:text-emerald-950">
                    {{ $totalTasks }} task guides
                </div>
            </div>
        </div>
        <span class="flex-shrink-0 text-emerald-100 transition-transform group-hover:translate-x-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </span>
    </button>
</div>
