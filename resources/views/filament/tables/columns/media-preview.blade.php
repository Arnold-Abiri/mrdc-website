@php($media = $getRecord())

@if (str_starts_with($media->mime_type, 'image/'))
    <a href="{{ route('admin.media.preview', $media->id) }}" target="_blank" rel="noopener" aria-label="Open {{ $media->original_filename }}">
        <img
            src="{{ route('admin.media.file', $media->id) }}"
            alt="{{ $media->alt_text ?: $media->title }}"
            loading="lazy"
            style="width: 52px; height: 52px; object-fit: cover; border-radius: 50%;"
        >
    </a>
@else
    <a href="{{ route('admin.media.preview', $media->id) }}" target="_blank" rel="noopener">
        @if ($media->mime_type === 'application/pdf')
            <span style="display: inline-flex; align-items: center; color: #b42318;" aria-label="PDF: {{ $media->original_filename }}" title="{{ $media->original_filename }}">
                <x-filament::icon icon="heroicon-o-document-text" style="width: 28px; height: 28px;" />
            </span>
        @else
            <span style="display: inline-flex; align-items: center;" aria-label="File: {{ $media->original_filename }}" title="{{ $media->original_filename }}">
                <x-filament::icon icon="heroicon-o-document" style="width: 28px; height: 28px;" />
            </span>
        @endif
    </a>
@endif
