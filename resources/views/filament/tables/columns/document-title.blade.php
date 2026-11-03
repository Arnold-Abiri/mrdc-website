@php($document = $getRecord())

<div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
    <x-filament::icon icon="heroicon-o-document-text" style="width: 22px; height: 22px; flex: none; color: #0b8f62;" />
    <span title="{{ $document->title }}" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
        {{ \Illuminate\Support\Str::limit($document->title, 30) }}
    </span>
</div>
