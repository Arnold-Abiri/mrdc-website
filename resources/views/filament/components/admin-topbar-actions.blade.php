<div class="mrdc-topbar-actions">
    <a class="mrdc-topbar-visit" href="{{ url('/') }}" target="_blank" rel="noopener noreferrer">
        <span>Visit Website</span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5h6v6m0-6-9 9M19 13v5a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h5"/></svg>
    </a>
    @if (\App\Filament\Resources\Enquiries\EnquiryResource::canViewAny())
        @php($newEnquiries = \App\Filament\Resources\Enquiries\EnquiryResource::getEloquentQuery()->where('status', 'new')->count())
        <a class="mrdc-topbar-alerts" href="{{ \App\Filament\Resources\Enquiries\EnquiryResource::getUrl() }}" aria-label="{{ $newEnquiries }} new enquiries" title="New enquiries">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM10 21h4"/></svg>
            @if ($newEnquiries > 0)<span class="mrdc-topbar-alert-count">{{ $newEnquiries > 9 ? '9+' : $newEnquiries }}</span>@endif
        </a>
    @endif
</div>
