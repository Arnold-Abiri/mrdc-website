@php
    use Filament\Support\Facades\FilamentView;
    use Filament\View\PanelsRenderHook;
@endphp

<div class="mrdc-auth-page min-h-screen w-full flex flex-col lg:flex-row bg-[#F8FAFC] text-slate-800 antialiased selection:bg-emerald-500 selection:text-white relative">
    {{-- Left Showcase Hero Section --}}
    <section class="mrdc-auth-hero relative flex flex-col justify-between w-full lg:w-[58%] xl:w-[60%] min-h-[460px] sm:min-h-[500px] lg:min-h-screen text-white overflow-hidden p-6 sm:p-10 lg:p-14 z-10">
        <div class="absolute inset-0 z-0">
            <img 
                src="{{ asset('images/hero-clean.webp') }}" 
                alt="Scenic Mutoko Landscape and Rocky Hills" 
                class="w-full h-full object-cover object-center filter brightness-[0.92] contrast-[1.05]"
            />
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/85 via-slate-950/55 to-slate-950/30 lg:to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/30 to-slate-950/40"></div>
        </div>

        <header class="relative z-10 pt-2 sm:pt-4">
            <div class="inline-flex items-center gap-2">
                <span class="w-8 h-1 bg-[#10b981] rounded-full"></span>
                <span class="text-xs sm:text-sm font-bold tracking-[0.18em] uppercase text-emerald-300">
                    Mutoko Rural District Council
                </span>
            </div>

            <h1 class="mt-4 text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-extrabold tracking-tight text-white leading-[1.12] max-w-xl drop-shadow-sm">
                Service Delivery for Sustainable Communities
            </h1>

            <p class="mt-4 text-sm sm:text-base lg:text-lg text-slate-200/90 max-w-lg leading-relaxed font-normal">
                Partnering with our people to build a cleaner, safer, healthier and more prosperous Mutoko.
            </p>

            <div class="mt-8 sm:mt-10 grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-5 max-w-2xl">
                <div class="flex flex-col items-center text-center group">
                    <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-full bg-[#059669] text-white flex items-center justify-center shadow-lg shadow-emerald-900/40 ring-4 ring-white/10 group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="mt-2.5 text-xs sm:text-sm font-bold text-white tracking-wide">Our People</span>
                    <span class="text-[11px] text-slate-300 font-light">Inclusive communities</span>
                </div>

                <div class="flex flex-col items-center text-center group">
                    <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-full bg-[#0284c7] text-white flex items-center justify-center shadow-lg shadow-sky-900/40 ring-4 ring-white/10 group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="mt-2.5 text-xs sm:text-sm font-bold text-white tracking-wide">Our Environment</span>
                    <span class="text-[11px] text-slate-300 font-light">A cleaner greener Mutoko</span>
                </div>

                <div class="flex flex-col items-center text-center group">
                    <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-full bg-[#d97706] text-white flex items-center justify-center shadow-lg shadow-amber-900/40 ring-4 ring-white/10 group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <span class="mt-2.5 text-xs sm:text-sm font-bold text-white tracking-wide">Our Development</span>
                    <span class="text-[11px] text-slate-300 font-light">Opportunities for growth</span>
                </div>

                <div class="flex flex-col items-center text-center group">
                    <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-full bg-[#475569] text-white flex items-center justify-center shadow-lg shadow-slate-900/40 ring-4 ring-white/10 group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <span class="mt-2.5 text-xs sm:text-sm font-bold text-white tracking-wide">Our Governance</span>
                    <span class="text-[11px] text-slate-300 font-light">Transparent and accountable</span>
                </div>
            </div>
        </header>

        <footer class="relative z-10 mt-10 lg:mt-14 mb-2 lg:mb-0">
            <div class="mrdc-vision-box relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#044030]/95 via-[#065F46]/95 to-[#0B8F62]/95 border border-emerald-500/30 shadow-2xl backdrop-blur-md p-6 sm:p-7">
                <div 
                    class="absolute inset-0 opacity-10 pointer-events-none mix-blend-overlay bg-repeat bg-center"
                    style="background-image: url('{{ asset('images/bottom-pattern.png') }}'); background-size: 160px;"
                ></div>

                <div class="relative z-10 grid grid-cols-1 sm:grid-cols-2 gap-6 sm:gap-8 items-start">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-1 bg-[#4ade80] rounded-full"></span>
                            <h3 class="text-sm font-bold tracking-wider uppercase text-emerald-300">Vision</h3>
                        </div>
                        <p class="mt-2 text-xs sm:text-sm text-slate-100 font-medium leading-relaxed">
                            A prosperous, inclusive and sustainable Mutoko.
                        </p>
                    </div>

                    <div class="sm:border-l sm:border-emerald-400/20 sm:pl-8">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold tracking-wider uppercase text-emerald-300">Mission</h3>
                        </div>
                        <p class="mt-2 text-xs sm:text-sm text-slate-100 font-medium leading-relaxed">
                            To provide quality services and promote local development through effective governance and stakeholder engagement.
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </section>

    {{-- Right Choose Password Section --}}
    <main class="mrdc-auth-form-panel relative flex-1 flex flex-col justify-between items-center w-full lg:w-[42%] xl:w-[40%] bg-gradient-to-b from-slate-50 to-slate-100/90 py-8 px-4 sm:px-8 lg:px-12 z-20 min-h-screen lg:min-h-0">
        <div class="w-full max-w-[420px] flex flex-col items-center my-auto">
            <a href="{{ url('/') }}" class="flex flex-col items-center text-center group mb-6 focus:outline-none" title="Return to Mutoko RDC Website">
                <img 
                    src="{{ asset('images/council-crest-light.webp') }}"
                    alt="Mutoko Rural District Council Crest" 
                    class="w-20 h-20 sm:w-24 sm:h-24 object-contain drop-shadow-sm group-hover:scale-105 transition-transform duration-200"
                />
                <h2 class="mt-3 text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight leading-snug">
                    Mutoko<br class="hidden sm:inline" /> Rural District Council
                </h2>
                <p class="mt-1 text-xs text-slate-500 font-medium">
                    Service Delivery for Sustainable Communities
                </p>
            </a>

            <div class="w-full bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-200/80 p-6 sm:p-8 relative">
                <div class="text-center mb-6">
                    <h3 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                        {{ $this->getHeading() }}
                    </h3>
                    @if (filled($subheading = $this->getSubheading()))
                        <p class="mt-1.5 text-xs sm:text-sm text-slate-500 font-normal">
                            {{ $subheading }}
                        </p>
                    @endif
                </div>

                {{ FilamentView::renderHook(PanelsRenderHook::SIMPLE_PAGE_START, scopes: $this->getRenderHookScopes()) }}

                <div class="mrdc-filament-form-wrapper">
                    {{ $this->content }}
                </div>

                <x-filament-actions::modals />

                {{ FilamentView::renderHook(PanelsRenderHook::SIMPLE_PAGE_END, scopes: $this->getRenderHookScopes()) }}

                <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                    <a 
                        href="{{ filament()->getLoginUrl() }}"
                        class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-[#0B8F62] hover:text-[#075D46] transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Back to Sign In</span>
                    </a>
                </div>
            </div>

            <div class="mt-6 text-center text-xs text-slate-500">
                <span>Need help? </span>
                <a 
                    href="mailto:support@mutokordc.gov.zw" 
                    class="font-medium text-[#0B8F62] hover:text-[#075D46] hover:underline transition-colors"
                >
                    Contact the System Administrator
                </a>
            </div>
        </div>

        <div class="w-full text-center mt-6 text-[11px] text-slate-400">
            &copy; {{ date('Y') }} Mutoko Rural District Council. All rights reserved.
        </div>
    </main>
</div>
