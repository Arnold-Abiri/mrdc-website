import { useCallback, useEffect, useMemo, useRef } from 'react';
import { createPortal } from 'react-dom';
import { usePublicTranslation } from '../../usePublicTranslation';
import { extractSources, SourceButtons } from './SourceButtons';

export type GalleryImage = {
    url: string;
    alt: string;
    caption: string | null;
    width: number | null;
    height: number | null;
};

type Props = {
    images: GalleryImage[];
    index: number;
    onClose: () => void;
    onIndexChange: (index: number) => void;
};

const FOCUSABLE = 'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])';

/**
 * Accessible, keyboard- and touch-driven image viewer.
 *
 * Renders in a portal so it always covers the viewport, traps focus while
 * open, locks body scroll, restores focus on close, and supports arrow keys,
 * Home/End, Escape, swipe gestures and thumbnail navigation.
 */
export default function GalleryLightbox({ images, index, onClose, onIndexChange }: Props) {
    const t = usePublicTranslation();
    const dialogRef = useRef<HTMLDivElement>(null);
    const restoreFocusRef = useRef<HTMLElement | null>(null);
    const touchStartX = useRef<number | null>(null);
    const count = images.length;
    const current = images[index];

    const go = useCallback((next: number) => {
        if (count === 0) {
            return;
        }
        onIndexChange((next + count) % count);
    }, [count, onIndexChange]);

    useEffect(() => {
        restoreFocusRef.current = document.activeElement instanceof HTMLElement ? document.activeElement : null;
        const { overflow } = document.body.style;
        document.body.style.overflow = 'hidden';
        dialogRef.current?.focus();

        return () => {
            document.body.style.overflow = overflow;
            restoreFocusRef.current?.focus();
        };
    }, []);

    useEffect(() => {
        function onKeyDown(event: KeyboardEvent) {
            if (event.key === 'Escape') {
                event.preventDefault();
                onClose();
                return;
            }
            if (event.key === 'ArrowRight') {
                event.preventDefault();
                go(index + 1);
                return;
            }
            if (event.key === 'ArrowLeft') {
                event.preventDefault();
                go(index - 1);
                return;
            }
            if (event.key === 'Home') {
                event.preventDefault();
                go(0);
                return;
            }
            if (event.key === 'End') {
                event.preventDefault();
                go(count - 1);
                return;
            }
            if (event.key !== 'Tab' || !dialogRef.current) {
                return;
            }
            const focusable = Array.from(dialogRef.current.querySelectorAll<HTMLElement>(FOCUSABLE)).filter(element => element.offsetParent !== null || element === document.activeElement);
            if (focusable.length === 0) {
                return;
            }
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }

        window.addEventListener('keydown', onKeyDown);

        return () => window.removeEventListener('keydown', onKeyDown);
    }, [count, go, index, onClose]);

    const aspect = useMemo(() => {
        if (current?.width && current?.height) {
            return `${current.width} / ${current.height}`;
        }

        return '3 / 2';
    }, [current]);

    if (!current) {
        return null;
    }

    return createPortal(
        <div
            className="gallery-lightbox"
            role="dialog"
            aria-modal="true"
            aria-label={t('imageViewer')}
            tabIndex={-1}
            ref={dialogRef}
            onClick={event => {
                if (event.target === event.currentTarget) {
                    onClose();
                }
            }}
            onTouchStart={event => {
                touchStartX.current = event.touches[0]?.clientX ?? null;
            }}
            onTouchEnd={event => {
                if (touchStartX.current === null || !event.changedTouches[0]) {
                    return;
                }
                const delta = event.changedTouches[0].clientX - touchStartX.current;
                touchStartX.current = null;
                if (Math.abs(delta) > 48) {
                    go(index + (delta < 0 ? 1 : -1));
                }
            }}
        >
            <div className="gallery-lightbox-bar">
                <p className="gallery-lightbox-counter" aria-live="polite">
                    <strong>{index + 1}</strong>
                    <span aria-hidden="true">/</span>
                    <span className="gallery-lightbox-total">{count}</span>
                </p>
                <button type="button" className="gallery-lightbox-close" onClick={onClose} aria-label={t('closeViewer')}>
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" aria-hidden="true">
                        <line x1="5" y1="5" x2="19" y2="19" />
                        <line x1="19" y1="5" x2="5" y2="19" />
                    </svg>
                </button>
            </div>

            <button type="button" className="gallery-lightbox-nav gallery-lightbox-prev" onClick={() => go(index - 1)} aria-label={t('previousImage')} disabled={count < 2}>
                <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                    <polyline points="15 18 9 12 15 6" />
                </svg>
            </button>

            <figure className="gallery-lightbox-figure">
                <img
                    key={current.url}
                    className="gallery-lightbox-image"
                    style={{ aspectRatio: aspect }}
                    src={current.url}
                    alt={current.alt}
                />
                {current.caption && (
                    <figcaption className="gallery-lightbox-caption">
                        <span>{extractSources(current.caption).body}</span>
                        <SourceButtons sources={extractSources(current.caption).sources} />
                    </figcaption>
                )}
            </figure>

            <button type="button" className="gallery-lightbox-nav gallery-lightbox-next" onClick={() => go(index + 1)} aria-label={t('nextImage')} disabled={count < 2}>
                <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                    <polyline points="9 18 15 12 9 6" />
                </svg>
            </button>

            {count > 1 && (
                <ul className="gallery-lightbox-strip" aria-label={t('openImage')}>
                    {images.map((image, imageIndex) => (
                        <li key={image.url}>
                            <button
                                type="button"
                                className={imageIndex === index ? 'gallery-lightbox-thumb is-active' : 'gallery-lightbox-thumb'}
                                onClick={() => go(imageIndex)}
                                aria-label={`${image.alt} (${imageIndex + 1} / ${count})`}
                                aria-current={imageIndex === index}
                            >
                                <img src={image.url} alt="" loading="lazy" />
                            </button>
                        </li>
                    ))}
                </ul>
            )}
        </div>,
        document.body,
    );
}
