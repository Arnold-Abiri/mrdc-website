@php($media = $getRecord())

@if (str_starts_with($media->mime_type, 'image/'))
    <a href="{{ route('admin.media.preview', $media->id) }}" target="_blank" rel="noopener" aria-label="Open preview for {{ $media->original_filename }}" style="display: inline-block;">
        <img
            src="{{ route('admin.media.file', $media->id) }}"
            alt="{{ $media->alt_text ?: $media->title }}"
            loading="lazy"
            style="width: 44px; height: 44px; object-fit: cover; border-radius: 8px; border: 1px solid #E2E8F0; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: transform 0.15s ease;"
            onmouseover="this.style.transform='scale(1.08)'"
            onmouseout="this.style.transform='scale(1)'"
        >
    </a>
@else
    <a href="{{ route('admin.media.preview', $media->id) }}" target="_blank" rel="noopener" style="display: inline-flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 8px; background: #F8FAFC; border: 1px solid #E2E8F0;">
        @if ($media->mime_type === 'application/pdf')
            <span style="display: inline-flex; align-items: center; color: #DC2626;" aria-label="PDF: {{ $media->original_filename }}" title="{{ $media->original_filename }}">
                <x-filament::icon icon="heroicon-o-document-text" style="width: 24px; height: 24px;" />
            </span>
        @else
            <span style="display: inline-flex; align-items: center; color: #475569;" aria-label="File: {{ $media->original_filename }}" title="{{ $media->original_filename }}">
                <x-filament::icon icon="heroicon-o-document" style="width: 24px; height: 24px;" />
            </span>
        @endif
    </a>
@endif
