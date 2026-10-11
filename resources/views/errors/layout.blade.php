@php
    $code = $code ?? 500;
    $eyebrow = $eyebrow ?? 'Mutoko Rural District Council';
    $title = $title ?? 'Something went wrong';
    $message = $message ?? 'The page you requested could not be loaded. Please try again or contact the council registry.';
    $home = url('/en');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $code }} — {{ $title }} | Mutoko Rural District Council</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <style>
        :root {
            --navy: #0B2D4D;
            --green: #0B8F62;
            --deep-green: #075D46;
            --gold: #B45309;
            --gold-strong: #D97706;
            --page: #F6F8FA;
            --ink: #17212B;
            --muted: #607080;
            --border: #D9E2E8;
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
        }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--page); color: var(--ink); line-height: 1.6; }
        .hero { background: linear-gradient(90deg, #021E18, #0B2D4D 70%); color: #fff; padding: 56px 0; }
        .wrap { width: min(100% - 32px, 760px); margin-inline: auto; }
        .eyebrow { display: inline-flex; align-items: center; gap: 8px; color: #FDE68A; font-size: .75rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; margin: 0 0 10px; }
        .eyebrow::before { content: ''; width: 24px; height: 3px; background: var(--gold-strong); border-radius: 2px; }
        h1 { font-size: clamp(2rem, 5vw, 3rem); line-height: 1.15; letter-spacing: -.02em; margin: 0 0 12px; }
        .code { display: inline-block; font-size: .85rem; font-weight: 800; letter-spacing: .1em; border: 1px solid rgba(255,255,255,.4); border-radius: 999px; padding: 2px 12px; margin-bottom: 14px; }
        .lede { color: #E2E8F0; font-size: 1.05rem; margin: 0; max-width: 640px; }
        .card { background: #fff; border: 1px solid var(--border); border-radius: 16px; padding: 28px; margin: -28px auto 0; box-shadow: 0 8px 24px rgba(11,45,77,.08); }
        .card p { color: var(--muted); margin: 0 0 20px; }
        .actions { display: flex; flex-wrap: wrap; gap: 12px; }
        .btn { display: inline-flex; align-items: center; gap: 8px; min-height: 48px; padding: 12px 24px; border-radius: 999px; font-weight: 700; font-size: .95rem; text-decoration: none; }
        .btn-primary { background: var(--deep-green); color: #fff; }
        .btn-outline { border: 1.5px solid var(--border); color: var(--navy); background: #fff; }
        .meta { margin-top: 20px; font-size: .85rem; color: var(--muted); }
        footer { padding: 32px 0 48px; font-size: .85rem; color: var(--muted); }
        footer a { color: var(--navy); }
    </style>
</head>
<body>
    <header class="hero">
        <div class="wrap">
            <p class="eyebrow">{{ $eyebrow }}</p>
            <span class="code">ERROR {{ $code }}</span>
            <h1>{{ $title }}</h1>
            <p class="lede">{{ $message }}</p>
        </div>
    </header>
    <main class="wrap">
        <div class="card">
            <p>If you typed the address, check the spelling and try again. You can also search the site or contact the council registry for assistance.</p>
            <div class="actions">
                <a class="btn btn-primary" href="{{ $home }}">Return to homepage <span aria-hidden="true">→</span></a>
                <a class="btn btn-outline" href="{{ url('/en/contact') }}">Contact the council</a>
                <a class="btn btn-outline" href="{{ url('/en/search') }}">Search the site</a>
            </div>
            @if (! empty($reference))
                <p class="meta">Reference: {{ $reference }}. Please quote this reference when contacting the registry.</p>
            @endif
        </div>
        <footer>
            <p>Mutoko Rural District Council · Stand 366, Mutoko Centre · <a href="mailto:registry@mutokordc.gov.zw">registry@mutokordc.gov.zw</a> · +263 77 240 5683</p>
        </footer>
    </main>
</body>
</html>
