<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MediaPreviewController extends Controller
{
    public function page(Media $media): Response
    {
        Gate::authorize('view', $media);
        $disk = Storage::disk(config('cms.media_disk'));
        abort_unless($disk->exists($media->storage_path), 404);

        $fileUrl = route('admin.media.file', $media->id);
        $isImage = str_starts_with($media->mime_type, 'image/');
        $html = Blade::render(<<<'BLADE'
            <!DOCTYPE html>
            <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>{{ $media->title }} — Media preview</title>
                <style>
                    :root { color-scheme: light; font-family: Inter, ui-sans-serif, system-ui, sans-serif; color: #17324d; background: #f3f6f8; }
                    * { box-sizing: border-box; }
                    body { margin: 0; min-height: 100vh; }
                    header { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 16px clamp(16px, 4vw, 48px); background: #fff; border-bottom: 1px solid #dbe3e8; }
                    a { color: #0b6955; font-weight: 650; text-decoration: none; }
                    a:hover { text-decoration: underline; }
                    .brand { color: #17324d; font-size: 14px; font-weight: 750; letter-spacing: .02em; }
                    main { width: min(1180px, calc(100% - 32px)); margin: 28px auto; }
                    .toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 16px; }
                    h1 { margin: 0; font-size: clamp(20px, 3vw, 28px); overflow-wrap: anywhere; }
                    .actions { display: flex; gap: 10px; }
                    .button { display: inline-flex; align-items: center; justify-content: center; min-height: 40px; padding: 9px 14px; border: 1px solid #cbd7df; border-radius: 8px; background: #fff; }
                    .button.primary { color: #fff; background: #0b6955; border-color: #0b6955; }
                    .viewer { display: grid; place-items: center; min-height: 52vh; padding: clamp(12px, 3vw, 28px); overflow: hidden; border: 1px solid #dbe3e8; border-radius: 14px; background: #fff; box-shadow: 0 8px 30px rgba(23, 50, 77, .06); }
                    img { display: block; max-width: 100%; max-height: calc(100vh - 250px); width: auto; height: auto; object-fit: contain; }
                    iframe { display: block; width: 100%; height: min(76vh, 920px); border: 0; }
                    .details { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 14px 24px; margin-top: 18px; padding: 18px 20px; border: 1px solid #dbe3e8; border-radius: 12px; background: #fff; }
                    dt { margin-bottom: 4px; color: #617588; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; }
                    dd { margin: 0; overflow-wrap: anywhere; font-size: 14px; }
                    @media (max-width: 560px) { header { align-items: flex-start; } .toolbar { align-items: stretch; flex-direction: column; } .actions { width: 100%; } .button { flex: 1; } .viewer { min-height: 40vh; } }
                </style>
            </head>
            <body>
                <header><a class="brand" href="{{ url('/admin/content/media') }}">Mutoko RDC · Media library</a><a href="{{ url('/admin/content/media/'.$media->id.'/edit') }}">Back to media record</a></header>
                <main>
                    <div class="toolbar"><h1>{{ $media->title }}</h1><div class="actions"><a class="button" href="{{ $fileUrl }}" target="_blank" rel="noopener">Open file</a><a class="button primary" href="{{ $fileUrl }}?download=1">Download</a></div></div>
                    <section class="viewer" aria-label="File preview">
                        @if ($isImage)
                            <img src="{{ $fileUrl }}" alt="{{ $media->alt_text ?: $media->title }}">
                        @elseif ($media->mime_type === 'application/pdf')
                            <iframe src="{{ $fileUrl }}#view=FitH" title="{{ $media->title }}"></iframe>
                        @else
                            <p>Preview is unavailable for this file type. Use Open file or Download.</p>
                        @endif
                    </section>
                    <dl class="details">
                        <div><dt>Filename</dt><dd>{{ $media->original_filename }}</dd></div>
                        <div><dt>File type</dt><dd>{{ $media->mime_type }}</dd></div>
                        <div><dt>Size</dt><dd>{{ $media->size ? number_format($media->size / 1024, 1).' KB' : 'Unknown' }}</dd></div>
                        @if (filled($media->alt_text)) <div><dt>Alt text</dt><dd>{{ $media->alt_text }}</dd></div> @endif
                        @if (filled($media->caption)) <div><dt>Caption</dt><dd>{{ $media->caption }}</dd></div> @endif
                    </dl>
                </main>
            </body>
            </html>
            BLADE, ['media' => $media, 'fileUrl' => $fileUrl, 'isImage' => $isImage]);

        return response($html)->header('Cache-Control', 'private, no-store');
    }

    public function show(Request $request, Media $media): Response
    {
        Gate::authorize('view', $media);
        $disk = Storage::disk(config('cms.media_disk'));
        abort_unless($disk->exists($media->storage_path), 404);

        return $disk->response($media->storage_path, $media->original_filename, [
            'Content-Type' => $media->mime_type,
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
            'Cache-Control' => 'private, no-store',
        ], $request->boolean('download') ? 'attachment' : 'inline');
    }
}
