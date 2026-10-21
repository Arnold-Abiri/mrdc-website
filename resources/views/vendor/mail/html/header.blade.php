@props(['url'])
@php
    $logoMediaId = \App\Models\SiteSetting::get('logo_media_id');
    $logoUrl = $logoMediaId && ctype_digit((string) $logoMediaId)
        ? public_route('managed-media.show', (int) $logoMediaId)
        : asset('images/council-crest-light.webp');
    $councilName = config('mail.from.name', 'Mutoko Rural District Council');
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
<table align="center" cellpadding="0" cellspacing="0" role="presentation" style="margin: 0 auto; text-align: center;">
<tr>
<td align="center" style="padding-bottom: 8px;">
<img src="{{ $logoUrl }}" class="logo" alt="{{ $councilName }}" style="height: 68px; max-height: 68px; width: auto; display: block; margin: 0 auto;">
</td>
</tr>
<tr>
<td align="center">
<span style="font-size: 18px; font-weight: 700; color: #065F46; letter-spacing: -0.01em; display: block; line-height: 1.25; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
{{ $councilName }}
</span>
<span style="font-size: 11px; font-weight: 500; color: #64748b; letter-spacing: 0.04em; text-transform: uppercase; display: block; margin-top: 4px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
Service Delivery for Sustainable Communities
</span>
</td>
</tr>
</table>
</a>
</td>
</tr>

