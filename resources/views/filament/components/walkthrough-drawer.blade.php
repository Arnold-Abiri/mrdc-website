@php
    $workflows = \App\Filament\Pages\SystemGuide::getWorkflowCatalog();
    $defaultCategory = \App\Filament\Pages\SystemGuide::getDefaultCategoryForUser();
@endphp

<div 
    class="mrdc-walkthrough-drawer print:hidden"
    x-data="{ 
        open: false, 
        isExpanded: false,
        filter: '',
        selectedCategory: '{{ $defaultCategory }}',
        workflows: {{ json_encode($workflows) }},
        matches(task, catId) {
            if (this.selectedCategory !== 'all' && this.selectedCategory !== catId) {
                return false;
            }
            if (!this.filter.trim()) return true;
            const q = this.filter.toLowerCase();
            return (task.title || '').toLowerCase().includes(q) 
                || (task.keywords || '').toLowerCase().includes(q)
                || (task.steps || []).some(s => s.toLowerCase().includes(q));
        },
        countVisibleTasks() {
            let count = 0;
            for (const cat of this.workflows) {
                for (const t of cat.tasks) {
                    if (this.matches(t, cat.id)) count++;
                }
            }
            return count;
        }
    }" 
    @open-walkthrough-drawer.window="open = true; $nextTick(() => $refs.drawerSearchInput?.focus())"
    x-cloak
>
    {{-- Slide-over Drawer Backdrop --}}
    <div 
        x-show="open" 
        x-transition:enter="transition-opacity ease-out duration-250"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false" 
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[9998]"
        style="display: none;"
    ></div>

    {{-- Slide-over Panel Content --}}
    <div 
        x-show="open" 
        role="dialog"
        aria-modal="true"
        aria-labelledby="workflow-drawer-title"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        @keydown.escape.window="open = false"
        :class="isExpanded ? 'w-full md:w-[85vw] lg:w-[75vw] max-w-6xl' : 'w-full md:w-[650px] lg:w-[720px] max-w-3xl'"
        class="fixed inset-y-0 right-0 bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800 shadow-2xl z-[9999] flex flex-col transition-all duration-300"
        style="display: none;"
    >
        {{-- Drawer Header --}}
        <div class="px-6 py-4.5 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/90 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3.5 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-xs flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div class="min-w-0">
                    <h3 id="workflow-drawer-title" class="font-bold text-base text-slate-900 dark:text-slate-100 truncate">Council Workflow Navigator</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Step-by-step procedures & navigation shortcuts</p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-shrink-0">
                <button 
                    type="button" 
                    @click="isExpanded = !isExpanded" 
                    :title="isExpanded ? 'Collapse panel width' : 'Expand panel width'"
                    class="hidden sm:inline-flex items-center justify-center p-2 rounded-lg text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                >
                    <svg x-show="!isExpanded" class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    <svg x-show="isExpanded" class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 4v4m0 0H5m4 0L4 3m11 1v4m0 0h4m-4 0l5-5M9 20v-4m0 0H5m4 0l-5 5m11-1v-4m0 0h4m-4 0l5 5"/></svg>
                </button>
                <a 
                    href="{{ \App\Filament\Pages\SystemGuide::getUrl() }}" 
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300 dark:hover:bg-emerald-900/60 border border-emerald-200 dark:border-emerald-800 transition-colors"
                    title="Open full page documentation"
                >
                    <span>Full Guide</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
                <button 
                    type="button" 
                    @click="open = false" 
                    aria-label="Close Walkthrough Drawer"
                    class="p-2 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Search Input in Drawer --}}
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 space-y-3">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input 
                    type="text" 
                    x-ref="drawerSearchInput"
                    x-model="filter" 
                    placeholder="Search any process (e.g. 'tender', 'complaint', 'minutes', 'user')..." 
                    class="w-full pl-10 pr-9 py-2.5 text-sm rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                />
                <button 
                    type="button" 
                    x-show="filter.length > 0" 
                    @click="filter = ''; $refs.drawerSearchInput?.focus()" 
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                >
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Quick Filter Tags --}}
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-thin">
                <button 
                    type="button"
                    @click="selectedCategory = 'all'"
                    :class="selectedCategory === 'all' 
                        ? 'bg-emerald-600 text-white font-semibold shadow-xs' 
                        : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                    class="px-3 py-1.5 rounded-full text-xs transition-colors cursor-pointer whitespace-nowrap"
                >
                    All Procedures
                </button>
                <template x-for="c in workflows" :key="c.id">
                    <button 
                        type="button"
                        @click="selectedCategory = c.id"
                        :class="selectedCategory === c.id 
                            ? 'bg-emerald-600 text-white font-semibold shadow-xs' 
                            : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-3 py-1.5 rounded-full text-xs transition-colors cursor-pointer whitespace-nowrap"
                        x-text="c.category"
                    ></button>
                </template>
            </div>
        </div>

        {{-- Drawer Scrollable Content --}}
        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-6">
            {{-- Empty Search State --}}
            <div 
                x-show="countVisibleTasks() === 0" 
                class="py-14 text-center text-slate-400 dark:text-slate-500 space-y-2.5"
            >
                <svg class="w-12 h-12 mx-auto opacity-50 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">No matching procedures found for "<span x-text="filter"></span>"</p>
                <p class="text-xs text-slate-400">Try keywords like 'tender', 'meeting', 'enquiry', or 'project'.</p>
                <button 
                    type="button" 
                    @click="filter = ''; selectedCategory = 'all'" 
                    class="text-xs text-emerald-600 font-semibold underline mt-2 inline-block cursor-pointer"
                >
                    Reset filters
                </button>
            </div>

            <template x-for="cat in workflows" :key="cat.id">
                <div 
                    x-show="selectedCategory === 'all' || selectedCategory === cat.id"
                    class="space-y-3.5"
                >
                    <div class="flex items-center gap-2 pb-1.5 border-b border-slate-200 dark:border-slate-800">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300" x-text="cat.category"></h4>
                    </div>

                    <div class="space-y-3.5">
                        <template x-for="task in cat.tasks" :key="task.title">
                            <div 
                                x-show="matches(task, cat.id)" 
                                class="p-4 sm:p-5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 hover:bg-white dark:hover:bg-slate-800 transition-colors shadow-xs"
                            >
                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 mb-3">
                                    <div class="min-w-0">
                                        <div class="font-bold text-sm sm:text-base text-slate-900 dark:text-slate-100 leading-snug" x-text="task.title"></div>
                                        <div class="flex flex-wrap items-center gap-2 mt-1.5" x-show="task.role || task.time">
                                            <template x-if="task.role">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700" x-text="task.role"></span>
                                            </template>
                                            <template x-if="task.time">
                                                <span class="text-[11px] text-slate-400" x-text="'~' + task.time"></span>
                                            </template>
                                        </div>
                                    </div>
                                    <template x-if="task.destination">
                                        <a 
                                            :href="task.destination" 
                                            class="self-start flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 transition-colors shadow-2xs"
                                        >
                                            <span x-text="task.destination_label"></span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </a>
                                    </template>
                                </div>

                                <ol class="space-y-2 text-xs sm:text-sm text-slate-700 dark:text-slate-300 pl-4 list-decimal list-outside leading-relaxed">
                                    <template x-for="step in task.steps" :key="step">
                                        <li x-text="step"></li>
                                    </template>
                                </ol>

                                <template x-if="task.tips">
                                    <div class="mt-3.5 p-3 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-xs text-amber-900 dark:text-amber-200 border border-amber-200/60 dark:border-amber-900/50 flex items-start gap-2">
                                        <span class="font-bold flex-shrink-0">Council Tip:</span> <span class="leading-relaxed" x-text="task.tips"></span>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        {{-- Drawer Footer --}}
        <div class="px-6 py-3.5 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 flex items-center justify-between text-xs text-slate-500">
            <span class="text-xs font-medium">Mutoko RDC Admin Portal</span>
            <button 
                type="button" 
                @click="open = false" 
                class="px-3.5 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-semibold cursor-pointer"
            >
                Close (Esc)
            </button>
        </div>
    </div>
</div>
