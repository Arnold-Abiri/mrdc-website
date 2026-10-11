@php
    $logoMediaId = \App\Models\SiteSetting::get('logo_media_id');
    $logoUrl = $logoMediaId && ctype_digit($logoMediaId)
        ? public_route('managed-media.show', (int) $logoMediaId)
        : asset('images/council-crest-light.webp');
@endphp
<span class="mrdc-admin-brand">
    <img src="{{ $logoUrl }}" alt="" class="mrdc-admin-brand-mark mrdc-admin-brand-mark-light">
    <img src="{{ $logoUrl }}" alt="" class="mrdc-admin-brand-mark mrdc-admin-brand-mark-dark">
    <span class="mrdc-admin-brand-copy">
        <strong>Mutoko<br>Rural District Council</strong>
        <small>Service Delivery for Sustainable Communities</small>
    </span>
</span>
