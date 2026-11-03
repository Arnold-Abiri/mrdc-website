@php($gallery = $getRecord())
@php($cover = $gallery->coverMedia ?? $gallery->media->first())

@if ($cover)
    <a href="{{ route('admin.media.preview', $cover->id) }}" target="_blank" rel="noopener" aria-label="Preview {{ $gallery->title }} cover">
        <img
            src="{{ route('admin.media.file', $cover->id) }}"
            alt="{{ $cover->alt_text ?: $gallery->title }}"
            loading="lazy"
            style="width: 52px; height: 52px; object-fit: cover; border-radius: 50%;"
        >
    </a>
@else
    <x-filament::icon icon="heroicon-o-photo" style="width: 28px; height: 28px; color: #94a3b8;" />
@endif
