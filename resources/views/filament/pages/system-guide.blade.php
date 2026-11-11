<x-filament-panels::page>
    <div x-data="{
        search: '',
        activeCategory: '{{ $defaultCategory ?? 'all' }}',
        catalog: {{ json_encode($workflows) }},
        matches(task, catId) {
            // When actively typing a search query, search globally across all categories
            if (!this.search.trim()) {
                if (this.activeCategory !== 'all' && this.activeCategory !== catId) {
                    return false;
                }
                return true;
            }
            // If user explicitly selected a specific category while searching, respect that category filter unless 'all'
            if (this.activeCategory !== 'all' && this.activeCategory !== catId) {
                return false;
            }

            const query = this.search.toLowerCase().trim();
            const titleMatch = (task.title || '').toLowerCase().includes(query);
            const kwMatch = (task.keywords || '').toLowerCase().includes(query);
            const roleMatch = (task.role || '').toLowerCase().includes(query);
            const stepMatch = (task.steps || []).some(s => s.toLowerCase().includes(query));
            return titleMatch || kwMatch || roleMatch || stepMatch;
        },
        hasVisibleTasks(catId) {
            const group = this.catalog.find(g => g.id === catId);
            if (!group) return false;
            return group.tasks.some(t => this.matches(t, catId));
        },
        totalVisibleCount() {
            let total = 0;
            for (const g of this.catalog) {
                for (const t of g.tasks) {
                    if (this.matches(t, g.id)) total++;
                }
            }
            return total;
        }
    }" class="space-y-6">

        {{-- Hero Header / Quick Search --}}
        <div class="mrdc-guide-hero">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="max-w-2xl">
                    <div class="mrdc-guide-eyebrow flex items-center gap-2">
                        <x-filament::icon icon="heroicon-m-book-open" class="w-4 h-4 text-emerald-300 print:hidden" />
                        Mutoko Rural District Council • Admin User Guide
                    </div>
                    <h1>
                        Admin User Guide
                    </h1>
                    <p>
                        Find a task, follow the steps, and open the relevant admin page. Shortcuts reflect your permissions; ask an authorised colleague when an action is unavailable.
                    </p>
                </div>

                {{-- Action Tools: Print Manual --}}
                <div class="flex-shrink-0 flex items-center gap-2 print:hidden">
                    <button 
                        type="button" 
                        onclick="window.print()" 
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold bg-white/15 hover:bg-white/25 text-white border border-white/30 backdrop-blur-xs transition-colors cursor-pointer shadow-xs"
                        title="Print or export as PDF handbook"
                    >
                        <x-filament::icon icon="heroicon-m-printer" class="w-4 h-4" />
                        <span>Print Guide</span>
                    </button>
                </div>
            </div>

            {{-- Search Box (Screen only) --}}
            <div class="relative mt-5 max-w-3xl print:hidden">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-700 dark:text-emerald-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input 
                    type="text"
                    aria-label="Search admin tasks"
                    x-model.debounce.150ms="search"
                    @input="if (search.trim().length > 0 && activeCategory !== 'all') { activeCategory = 'all'; }"
                    placeholder="Search any process (e.g. 'publish tender', 'respond to enquiry', 'minutes', 'project status')..." 
                    class="mrdc-guide-search-box w-full pl-11 pr-10 py-3 text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-emerald-300"
                />
                <button 
                    type="button" 
                    x-show="search.length > 0" 
                    @click="search = ''" 
                    aria-label="Clear search"
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer"
                >
                    <x-filament::icon icon="heroicon-m-x-mark" class="w-5 h-5" />
                </button>
            </div>
        </div>

        <p class="text-sm text-slate-600 dark:text-slate-300 print:hidden">
            For current tasks and operational information, open the
            <a href="{{ route('filament.admin.pages.council-dashboard') }}" class="font-medium text-emerald-700 underline dark:text-emerald-400">dashboard</a>.
        </p>

        {{-- Category Filter Pills (Screen only) --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-200 dark:border-slate-800 print:hidden">
            <button 
                type="button"
                @click="activeCategory = 'all'"
                :class="activeCategory === 'all' 
                    ? 'bg-emerald-600 text-white font-semibold shadow-sm' 
                    : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                class="px-3.5 py-1.5 rounded-full text-xs transition-colors cursor-pointer whitespace-nowrap border border-slate-200 dark:border-slate-700"
            >
                All Tasks ({{ collect($workflows)->sum(fn($w) => count($w['tasks'])) }})
            </button>

            @foreach ($workflows as $category)
                <button 
                    type="button"
                    @click="activeCategory = '{{ $category['id'] }}'"
                    :class="activeCategory === '{{ $category['id'] }}' 
                        ? 'bg-emerald-600 text-white font-semibold shadow-sm' 
                        : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                    class="px-3.5 py-1.5 rounded-full text-xs transition-colors cursor-pointer whitespace-nowrap border border-slate-200 dark:border-slate-700 flex items-center gap-1.5"
                >
                    <x-filament::icon :icon="$category['icon']" class="w-3.5 h-3.5" />
                    <span>{{ $category['category'] }}</span>
                    <span class="opacity-60 text-[10px]">({{ count($category['tasks']) }})</span>
                </button>
            @endforeach
        </div>

        {{-- Empty Search Results Feedback --}}
        <div 
            x-show="totalVisibleCount() === 0" 
            class="mrdc-empty-results py-16 text-center space-y-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-8 shadow-xs print:hidden"
            style="display: none;"
        >
            <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">No procedures found for "<span x-text="search"></span>"</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto">Try searching for common terms like "tender", "enquiry", "minutes", "project", or "user accounts".</p>
            <button 
                type="button" 
                @click="search = ''; activeCategory = 'all'" 
                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition-colors cursor-pointer"
            >
                Reset Search & Filters
            </button>
        </div>

        {{-- Workflow Cards Listing --}}
        <div class="space-y-8 mrdc-guide-workflows-container">
            @foreach ($workflows as $gIdx => $group)
                <div 
                    x-show="(activeCategory === 'all' || activeCategory === '{{ $group['id'] }}') && hasVisibleTasks('{{ $group['id'] }}')"
                    class="space-y-4 mrdc-procedure-group"
                >
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center text-emerald-700 dark:text-emerald-400">
                            <x-filament::icon :icon="$group['icon']" class="w-4 h-4" />
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">
                                {{ $group['category'] }}
                            </h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $group['description'] }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mrdc-procedure-grid">
                        @foreach ($group['tasks'] as $tIdx => $task)
                            @php
                                $taskStorageKey = 'mrdc_walkthrough_step_' . md5($task['title']);
                            @endphp
                            <div 
                                x-show="matches(catalog[{{ $gIdx }}].tasks[{{ $tIdx }}], '{{ $group['id'] }}')"
                                x-data="{ 
                                    storageKey: '{{ $taskStorageKey }}',
                                    checkedSteps: JSON.parse(localStorage.getItem('{{ $taskStorageKey }}') || '[]'),
                                    toggleStep(idx) {
                                        if (this.checkedSteps.includes(idx)) {
                                            this.checkedSteps = this.checkedSteps.filter(i => i !== idx);
                                        } else {
                                            this.checkedSteps.push(idx);
                                        }
                                        localStorage.setItem(this.storageKey, JSON.stringify(this.checkedSteps));
                                    },
                                    resetSteps() {
                                        this.checkedSteps = [];
                                        localStorage.removeItem(this.storageKey);
                                    }
                                }"
                                class="mrdc-procedure-card rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between"
                            >
                                <div>
                                    <div class="flex items-start justify-between gap-3 mb-2">
                                        <div>
                                            <h3 class="font-semibold text-slate-900 dark:text-slate-100 text-sm leading-snug">
                                                {{ $task['title'] }}
                                            </h3>
                                            @if (!empty($task['role']) || !empty($task['time']))
                                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                                    @if (!empty($task['role']))
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                                            {{ $task['role'] }}
                                                        </span>
                                                    @endif
                                                    @if (!empty($task['time']))
                                                        <span class="inline-flex items-center gap-1 text-[10px] text-slate-400">
                                                            <x-filament::icon icon="heroicon-m-clock" class="w-3 h-3" />
                                                            ~{{ $task['time'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        @if (!empty($task['destination']))
                                            <a 
                                                href="{{ $task['destination'] }}" 
                                                class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors print:hidden"
                                            >
                                                <span>{{ $task['destination_label'] }}</span>
                                                <x-filament::icon icon="heroicon-m-arrow-top-right-on-square" class="w-3.5 h-3.5" />
                                            </a>
                                        @endif
                                    </div>

                                    @if (empty($task['destination']))
                                        <p class="text-xs text-slate-500">This shortcut is unavailable for your account. Ask an authorised colleague for help with this task.</p>
                                    @endif

                                    {{-- Steps List with Interactive Checklist --}}
                                    <div class="space-y-2 my-3">
                                        <div class="flex items-center justify-between text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                                            <span>Steps:</span>
                                            <div class="flex items-center gap-2 print:hidden" x-show="checkedSteps.length > 0">
                                                <span class="text-[10px] font-normal normal-case text-slate-400">
                                                    <span x-text="checkedSteps.length"></span> of {{ count($task['steps']) }} completed
                                                </span>
                                                <button 
                                                    type="button" 
                                                    @click="resetSteps()" 
                                                    class="text-[10px] normal-case text-emerald-600 hover:underline cursor-pointer"
                                                    title="Reset checklist progress"
                                                >
                                                    Reset
                                                </button>
                                            </div>
                                        </div>
                                        <ol class="space-y-2 text-xs text-slate-600 dark:text-slate-300 list-none">
                                            @foreach ($task['steps'] as $idx => $step)
                                                <li 
                                                    class="flex items-start gap-2.5 p-1.5 rounded transition-colors"
                                                    :class="checkedSteps.includes({{ $idx }}) ? 'bg-emerald-50/50 dark:bg-emerald-950/20 text-slate-400 line-through' : ''"
                                                >
                                                    <span class="hidden print:inline-flex items-center justify-center w-4 h-4 rounded-full border border-slate-400 text-[10px] font-bold text-slate-700 flex-shrink-0 mt-0.5">
                                                        {{ $idx + 1 }}
                                                    </span>
                                                    <input 
                                                        type="checkbox" 
                                                        :checked="checkedSteps.includes({{ $idx }})" 
                                                        @change="toggleStep({{ $idx }})" 
                                                        aria-label="Mark step {{ $idx + 1 }} complete"
                                                        class="mt-0.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer print:hidden"
                                                    />
                                                    <span class="leading-relaxed flex-1 select-none">{{ $step }}</span>
                                                </li>
                                            @endforeach
                                        </ol>
                                    </div>

                                    @if (!empty($task['tips']))
                                        <div class="p-2.5 rounded-lg bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-900/40 text-[11px] text-amber-800 dark:text-amber-200 flex items-start gap-2">
                                            <x-filament::icon icon="heroicon-m-light-bulb" class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" />
                                            <div>
                                                <strong class="font-medium">Council Guideline:</strong> {{ $task['tips'] }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>

