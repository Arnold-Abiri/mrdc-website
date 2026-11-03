<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $media->title }} &mdash; Mutoko RDC Media Preview</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    <style>
        :root {
            color-scheme: light;
            --color-council-dark: #0B2D4D;
            --color-council-green: #0B8F62;
            --color-council-green-hover: #09724e;
            --color-council-gold: #D97706;
            --color-bg: #F8FAFC;
            --color-card-bg: #FFFFFF;
            --color-border: #E2E8F0;
            --color-border-dark: #CBD5E1;
            --color-text-main: #0F172A;
            --color-text-muted: #64748B;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --shadow-sm: 0 1px 2px 0 rgba(15, 23, 42, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(15, 23, 42, 0.07), 0 2px 4px -2px rgba(15, 23, 42, 0.07);
            --shadow-lg: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--color-bg);
            color: var(--color-text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Bar */
        .site-topbar {
            background: var(--color-council-dark);
            color: #E2E8F0;
            padding: 12px clamp(16px, 4vw, 40px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 2px solid var(--color-council-green);
        }

        .brand-link {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: #FFFFFF;
            text-decoration: none;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: -0.01em;
        }

        .brand-link:hover {
            color: #F8FAFC;
        }

        .brand-crest {
            width: 32px;
            height: 32px;
            object-fit: contain;
            border-radius: 4px;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #CBD5E1;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: var(--radius-md);
            border: 1px solid rgba(255, 255, 255, 0.15);
            background: rgba(255, 255, 255, 0.05);
            transition: all 0.15s ease;
        }

        .nav-back-link:hover {
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.3);
            text-decoration: none;
        }

        /* Main Container */
        .main-container {
            width: min(1200px, calc(100% - 32px));
            margin: 28px auto 48px;
            flex: 1;
        }

        /* Breadcrumb & Header */
        .preview-header {
            margin-bottom: 20px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--color-text-muted);
            margin-bottom: 8px;
            flex-wrap: wrap;
        }

        .breadcrumb a {
            color: var(--color-council-green);
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb-sep {
            color: #94A3B8;
        }

        .header-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .title-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            max-width: 720px;
        }

        .page-title {
            font-size: clamp(20px, 2.5vw, 26px);
            font-weight: 700;
            color: var(--color-council-dark);
            line-height: 1.25;
            overflow-wrap: anywhere;
        }

        .badge-row {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 9999px;
            border: 1px solid transparent;
            text-transform: capitalize;
        }

        .badge-active {
            background-color: #ECFDF5;
            color: #065F46;
            border-color: #A7F3D0;
        }

        .badge-archived {
            background-color: #F1F5F9;
            color: #475569;
            border-color: #CBD5E1;
        }

        .badge-type {
            background-color: #EFF6FF;
            color: #1E40AF;
            border-color: #BFDBFE;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.04em;
        }

        .badge-dims {
            background-color: #F8FAFC;
            color: #475569;
            border-color: #E2E8F0;
            font-family: monospace;
            font-size: 11px;
        }

        /* Action Buttons */
        .btn-toolbar {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-height: 38px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
            border-radius: var(--radius-md);
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            border: 1px solid var(--color-border-dark);
            background: #FFFFFF;
            color: var(--color-text-main);
            box-shadow: var(--shadow-sm);
        }

        .btn:hover {
            background: #F8FAFC;
            border-color: #94A3B8;
            color: var(--color-council-dark);
        }

        .btn-primary {
            background: var(--color-council-green);
            border-color: var(--color-council-green);
            color: #FFFFFF;
        }

        .btn-primary:hover {
            background: var(--color-council-green-hover);
            border-color: var(--color-council-green-hover);
            color: #FFFFFF;
        }

        /* Layout Grid */
        .preview-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 340px;
            gap: 24px;
            align-items: start;
        }

        /* Viewer Stage */
        .viewer-card {
            background: var(--color-card-bg);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .viewer-stage-header {
            padding: 12px 16px;
            background: #FAFBFD;
            border-bottom: 1px solid var(--color-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            color: var(--color-text-muted);
        }

        .stage-controls {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .stage-btn {
            background: none;
            border: 1px solid var(--color-border);
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            color: var(--color-text-muted);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .stage-btn:hover {
            background: #F1F5F9;
            color: var(--color-text-main);
        }

        .viewer-viewport {
            min-height: 480px;
            max-height: 75vh;
            display: grid;
            place-items: center;
            padding: 24px;
            position: relative;
            overflow: auto;
            background-color: #F8FAFC;
        }

        /* Checkerboard pattern for alpha transparency */
        .transparency-grid {
            background-color: #FAFAFA;
            background-image:
                linear-gradient(45deg, #EBEBEB 25%, transparent 25%),
                linear-gradient(-45deg, #EBEBEB 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, #EBEBEB 75%),
                linear-gradient(-45deg, transparent 75%, #EBEBEB 75%);
            background-size: 16px 16px;
            background-position: 0 0, 0 8px, 8px -8px, -8px 0px;
        }

        .viewer-image {
            max-width: 100%;
            max-height: 65vh;
            width: auto;
            height: auto;
            object-fit: contain;
            border-radius: var(--radius-md);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease;
            cursor: zoom-in;
        }

        .viewer-image.zoomed {
            max-width: none;
            max-height: none;
            cursor: zoom-out;
        }

        .viewer-pdf-frame {
            width: 100%;
            height: 72vh;
            min-height: 580px;
            border: 0;
            background: #FFFFFF;
        }

        .viewer-fallback {
            text-align: center;
            padding: 48px 24px;
            max-width: 420px;
            margin: auto;
        }

        .viewer-fallback-icon {
            color: var(--color-text-muted);
            margin-bottom: 12px;
        }

        .viewer-fallback-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--color-council-dark);
            margin-bottom: 6px;
        }

        .viewer-fallback-desc {
            font-size: 13px;
            color: var(--color-text-muted);
            margin-bottom: 16px;
        }

        /* Metadata Sidebar */
        .metadata-sidebar {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .meta-card {
            background: var(--color-card-bg);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 18px;
            box-shadow: var(--shadow-sm);
        }

        .meta-card-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--color-council-dark);
            padding-bottom: 10px;
            margin-bottom: 12px;
            border-bottom: 1px solid var(--color-border);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .meta-dl {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            font-size: 13px;
        }

        .meta-item dt {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--color-text-muted);
            margin-bottom: 2px;
        }

        .meta-item dd {
            font-size: 13px;
            color: var(--color-text-main);
            overflow-wrap: anywhere;
            font-weight: 500;
        }

        .code-tag {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            background: #F1F5F9;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
            border: 1px solid #E2E8F0;
        }

        /* Toast feedback */
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: var(--color-council-dark);
            color: #FFFFFF;
            padding: 10px 18px;
            border-radius: var(--radius-md);
            font-size: 13px;
            font-weight: 500;
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 8px;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.2s ease;
            pointer-events: none;
            z-index: 1000;
        }

        .toast.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Footer */
        .preview-footer {
            margin-top: auto;
            background: #FFFFFF;
            border-top: 1px solid var(--color-border);
            padding: 16px;
            text-align: center;
            font-size: 12px;
            color: var(--color-text-muted);
        }

        @media (max-width: 900px) {
            .preview-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 560px) {
            .site-topbar {
                flex-direction: column;
                align-items: stretch;
            }
            .header-row {
                flex-direction: column;
                align-items: stretch;
            }
            .btn-toolbar {
                width: 100%;
            }
            .btn {
                flex: 1;
            }
            .viewer-viewport {
                min-height: 320px;
                padding: 12px;
            }
        }
    </style>
</head>
<body>

    <!-- Council Header Bar -->
    <header class="site-topbar">
        <a class="brand-link" href="{{ url('/admin/content/media') }}">
            <img class="brand-crest" src="{{ asset('images/council-crest-light.webp') }}" alt="" onerror="this.style.display='none'">
            <span>Mutoko Rural District Council &middot; Media Library</span>
        </a>
        <div class="topbar-actions">
            <a class="nav-back-link" href="{{ url('/admin/content/media/'.$media->id.'/edit') }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Edit Record</span>
            </a>
            <a class="nav-back-link" href="{{ url('/admin/content/media') }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                <span>Media Index</span>
            </a>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="main-container">
        <!-- Breadcrumbs & Header -->
        <div class="preview-header">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ url('/admin') }}">Admin</a>
                <span class="breadcrumb-sep">/</span>
                <a href="{{ url('/admin/content/media') }}">Media Library</a>
                <span class="breadcrumb-sep">/</span>
                <span>{{ $media->title }}</span>
            </nav>

            <div class="header-row">
                <div class="title-group">
                    <h1 class="page-title">{{ $media->title }}</h1>
                    <div class="badge-row">
                        <span class="badge {{ $media->status === 'active' ? 'badge-active' : 'badge-archived' }}">
                            {{ $media->status ?? 'active' }}
                        </span>
                        <span class="badge badge-type">
                            {{ $fileExtension ?: $media->mime_type }}
                        </span>
                        @if ($media->width && $media->height)
                            <span class="badge badge-dims">
                                {{ $media->width }} &times; {{ $media->height }} px
                            </span>
                        @endif
                        <span class="badge badge-dims">
                            {{ $formattedSize }}
                        </span>
                    </div>
                </div>

                <!-- Quick Action Buttons -->
                <div class="btn-toolbar">
                    <button type="button" class="btn" id="copy-btn" data-url="{{ $fileUrl }}" onclick="copyFileUrl(this)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                        <span id="copy-btn-text">Copy Link</span>
                    </button>
                    <a class="btn" href="{{ $fileUrl }}" target="_blank" rel="noopener">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                        </svg>
                        <span>Open Raw</span>
                    </a>
                    <a class="btn btn-primary" href="{{ $fileUrl }}?download=1">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Download</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 2-Column Responsive Layout -->
        <div class="preview-layout">
            <!-- Stage Viewer -->
            <section class="viewer-card" aria-label="Media preview viewer">
                <div class="viewer-stage-header">
                    <span>Preview stage &middot; {{ $media->mime_type }}</span>
                    @if ($isImage)
                        <div class="stage-controls">
                            <button type="button" class="stage-btn" id="toggle-zoom-btn" onclick="toggleZoom()">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    <line x1="11" y1="8" x2="11" y2="14"></line>
                                    <line x1="8" y1="11" x2="14" y2="11"></line>
                                </svg>
                                <span id="zoom-label">Toggle Zoom</span>
                            </button>
                        </div>
                    @endif
                </div>

                <div class="viewer-viewport {{ $isImage ? 'transparency-grid' : '' }}">
                    @if ($isImage)
                        <img
                            id="preview-img"
                            class="viewer-image"
                            src="{{ $fileUrl }}"
                            alt="{{ $media->alt_text ?: $media->title }}"
                            onclick="toggleZoom()"
                            title="Click to toggle actual size"
                        >
                    @elseif ($media->mime_type === 'application/pdf')
                        <iframe
                            class="viewer-pdf-frame"
                            src="{{ $fileUrl }}#view=FitH"
                            title="{{ $media->title }}"
                        ></iframe>
                    @elseif (str_starts_with($media->mime_type, 'audio/'))
                        <div class="viewer-fallback">
                            <audio controls style="width: 100%;">
                                <source src="{{ $fileUrl }}" type="{{ $media->mime_type }}">
                                Your browser does not support audio playback.
                            </audio>
                        </div>
                    @elseif (str_starts_with($media->mime_type, 'video/'))
                        <div class="viewer-fallback">
                            <video controls style="max-width: 100%; border-radius: 8px;">
                                <source src="{{ $fileUrl }}" type="{{ $media->mime_type }}">
                                Your browser does not support video playback.
                            </video>
                        </div>
                    @else
                        <div class="viewer-fallback">
                            <div class="viewer-fallback-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                            </div>
                            <h2 class="viewer-fallback-title">Preview Unavailable</h2>
                            <p class="viewer-fallback-desc">Interactive browser preview is not supported for {{ $media->mime_type }}. Use the open or download button.</p>
                            <a class="btn btn-primary" href="{{ $fileUrl }}?download=1">Download File</a>
                        </div>
                    @endif
                </div>
            </section>

            <!-- Metadata Sidebar -->
            <aside class="metadata-sidebar" aria-label="Media details">
                <!-- File Details -->
                <div class="meta-card">
                    <h2 class="meta-card-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <span>File Information</span>
                    </h2>
                    <dl class="meta-dl">
                        <div class="meta-item">
                            <dt>Original Filename</dt>
                            <dd><span class="code-tag">{{ $media->original_filename }}</span></dd>
                        </div>
                        <div class="meta-item">
                            <dt>MIME Type</dt>
                            <dd><span class="code-tag">{{ $media->mime_type }}</span></dd>
                        </div>
                        <div class="meta-item">
                            <dt>File Size</dt>
                            <dd>{{ $formattedSize }} ({{ number_format($media->size) }} bytes)</dd>
                        </div>
                        @if ($media->width && $media->height)
                            <div class="meta-item">
                                <dt>Pixel Dimensions</dt>
                                <dd>{{ $media->width }} &times; {{ $media->height }} pixels</dd>
                            </div>
                            <div class="meta-item">
                                <dt>Aspect Ratio</dt>
                                <dd>{{ $aspectRatio }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- CMS & Governance Attribution -->
                <div class="meta-card">
                    <h2 class="meta-card-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Governance & Context</span>
                    </h2>
                    <dl class="meta-dl">
                        <div class="meta-item">
                            <dt>Department</dt>
                            <dd>{{ $media->department?->name ?? 'General Council / All Departments' }}</dd>
                        </div>
                        <div class="meta-item">
                            <dt>Uploaded By</dt>
                            <dd>{{ $media->uploader?->name ?? 'System Administrator' }}</dd>
                        </div>
                        <div class="meta-item">
                            <dt>Uploaded Date</dt>
                            <dd>{{ $media->created_at?->format('d M Y, H:i') ?? 'N/A' }}</dd>
                        </div>
                        <div class="meta-item">
                            <dt>Last Modified</dt>
                            <dd>{{ $media->updated_at?->format('d M Y, H:i') ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Accessibility & Content -->
                @if (filled($media->alt_text) || filled($media->caption))
                    <div class="meta-card">
                        <h2 class="meta-card-title">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                            <span>Accessibility & Content</span>
                        </h2>
                        <dl class="meta-dl">
                            @if (filled($media->alt_text))
                                <div class="meta-item">
                                    <dt>Alt Text (Accessibility)</dt>
                                    <dd>{{ $media->alt_text }}</dd>
                                </div>
                            @endif
                            @if (filled($media->caption))
                                <div class="meta-item">
                                    <dt>Editorial Caption</dt>
                                    <dd>{{ $media->caption }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                @endif
            </aside>
        </div>
    </main>

    <!-- Council Footer -->
    <footer class="preview-footer">
        <p>&copy; {{ date('Y') }} Mutoko Rural District Council &middot; Media Library</p>
    </footer>

    <!-- Copy Feedback Toast -->
    <div id="toast" class="toast" role="status" aria-live="polite">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#34D399" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <span id="toast-message">Link copied to clipboard</span>
    </div>

    <script>
        function copyFileUrl(button) {
            var url = button.getAttribute('data-url') || window.location.href;
            var textSpan = document.getElementById('copy-btn-text');

            function showSuccess() {
                var toast = document.getElementById('toast');
                var toastMsg = document.getElementById('toast-message');
                if (toastMsg) toastMsg.textContent = 'Link copied to clipboard';
                if (toast) {
                    toast.classList.add('visible');
                    setTimeout(function() {
                        toast.classList.remove('visible');
                    }, 2500);
                }
                if (textSpan) {
                    var prevText = textSpan.textContent;
                    textSpan.textContent = 'Copied!';
                    setTimeout(function() {
                        textSpan.textContent = prevText;
                    }, 2000);
                }
            }

            // Primary: Modern Async Clipboard API (requires secure context or localhost)
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(showSuccess).catch(function() {
                    fallbackCopy(url, showSuccess);
                });
            } else {
                // Robust Fallback: document.execCommand('copy') for non-secure HTTP / local dev domains (.test)
                fallbackCopy(url, showSuccess);
            }
        }

        function fallbackCopy(text, onSuccess) {
            var textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            textArea.setAttribute('readonly', '');
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                var successful = document.execCommand('copy');
                document.body.removeChild(textArea);
                if (successful) {
                    onSuccess();
                } else {
                    prompt('Copy this link:', text);
                }
            } catch (err) {
                document.body.removeChild(textArea);
                prompt('Copy this link:', text);
            }
        }

        function toggleZoom() {
            var img = document.getElementById('preview-img');
            var label = document.getElementById('zoom-label');
            if (!img) return;
            img.classList.toggle('zoomed');
            if (label) {
                label.textContent = img.classList.contains('zoomed') ? 'Fit Screen' : 'Actual Size';
            }
        }
    </script>
</body>
</html>
